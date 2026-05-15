<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InvoiceService
{
    /**
     * Create invoice with line items, stock deduction, and movements.
     *
     * @param  array  $payload  customer_id, pricing_type, discount_amount, tax_rate, paid_amount, notes, items: [['product_id'=>, 'quantity'=>]]
     */
    public function createInvoice(array $payload, User $user)
    {
        return DB::transaction(function () use ($payload, $user) {
            $customer = Customer::query()->lockForUpdate()->findOrFail($payload['customer_id']);

            $pricingType = $payload['pricing_type'];
            if (! in_array($pricingType, [Invoice::PRICING_RETAIL, Invoice::PRICING_WHOLESALE], true)) {
                throw new InvalidArgumentException('Invalid pricing type.');
            }

            $itemsInput = $payload['items'];
            if (! is_array($itemsInput) || count($itemsInput) === 0) {
                throw new InvalidArgumentException('Invoice must have at least one line item.');
            }

            $productIds = collect($itemsInput)->pluck('product_id')->unique()->filter()->all();
            $products = Product::query()->whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $lines = [];
            $subtotal = 0;

            foreach ($itemsInput as $row) {
                $pid = (int) ($row['product_id'] ?? 0);
                $qty = (int) ($row['quantity'] ?? 0);
                if ($pid <= 0 || $qty <= 0) {
                    throw new InvalidArgumentException('Each line needs a valid product and quantity.');
                }
                /** @var Product|null $product */
                $product = $products->get($pid);
                if (! $product) {
                    throw new InvalidArgumentException('Product not found: '.$pid);
                }
                if ($product->status !== Product::STATUS_ACTIVE) {
                    throw new InvalidArgumentException('Product is not active: '.$product->sku);
                }
                if ($qty > (int) $product->stock_quantity) {
                    throw new InvalidArgumentException('Insufficient stock for '.$product->name.' (SKU '.$product->sku.').');
                }

                $unitPrice = (float) $product->unitPriceForPricingType($pricingType);
                $lineTotal = round($unitPrice * $qty, 2);
                $subtotal += $lineTotal;

                $lines[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'unit_cost' => (float) $product->purchase_price,
                    'line_total' => $lineTotal,
                ];
            }

            $discount = max(0, (float) ($payload['discount_amount'] ?? 0));
            if ($discount > $subtotal) {
                $discount = $subtotal;
            }

            $taxRateRaw = isset($payload['tax_rate']) ? $payload['tax_rate'] : null;
            if ($taxRateRaw === '' || $taxRateRaw === null) {
                $taxRate = null;
                $taxAmount = 0;
            } else {
                $taxRate = (float) $taxRateRaw;
                $afterDiscount = max(0, $subtotal - $discount);
                $taxAmount = round($afterDiscount * ($taxRate / 100), 2);
            }

            $total = round(max(0, $subtotal - $discount + $taxAmount), 2);
            $paid = max(0, min((float) ($payload['paid_amount'] ?? 0), $total));

            $invoice = new Invoice();
            $invoice->customer_id = $customer->id;
            $invoice->pricing_type = $pricingType;
            $invoice->subtotal = (string) round($subtotal, 2);
            $invoice->discount_amount = (string) round($discount, 2);
            $invoice->tax_rate = $taxRate === null ? null : (string) $taxRate;
            $invoice->tax_amount = (string) $taxAmount;
            $invoice->total = (string) $total;
            $invoice->paid_amount = (string) $paid;
            $invoice->remaining_amount = (string) round($total - $paid, 2);
            $invoice->payment_status = Invoice::computePaymentStatus($total, $paid);
            $invoice->notes = $payload['notes'] ?? null;
            $invoice->created_by = $user->id;
            $invoice->save();

            foreach ($lines as $line) {
                /** @var Product $product */
                $product = $line['product'];
                $qty = $line['quantity'];

                InvoiceItem::query()->create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $line['unit_price'],
                    'unit_cost' => $line['unit_cost'],
                    'line_total' => $line['line_total'],
                ]);

                $product->stock_quantity = (int) $product->stock_quantity - $qty;
                $product->save();

                InventoryMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => InventoryMovement::TYPE_SALE,
                    'quantity' => -$qty,
                    'reference_type' => Invoice::class,
                    'reference_id' => $invoice->id,
                    'notes' => 'Sale invoice',
                ]);
            }

            $invoice->invoice_number = sprintf('INV-%s-%05d', date('Y'), $invoice->id);
            $invoice->save();

            $customer->recalculateDebt();

            return $invoice->fresh(['items.product', 'customer']);
        });
    }
}
