<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentService
{
    /**
     * Record a payment against an invoice and sync balances.
     */
    public function recordPayment(array $payload, User $user)
    {
        return DB::transaction(function () use ($payload, $user) {
            $customer = Customer::query()->lockForUpdate()->findOrFail($payload['customer_id']);
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($payload['invoice_id']);

            if ((int) $invoice->customer_id !== (int) $customer->id) {
                throw new InvalidArgumentException('Invoice does not belong to this customer.');
            }

            $amount = round((float) $payload['amount'], 2);
            if ($amount <= 0) {
                throw new InvalidArgumentException('Payment amount must be greater than zero.');
            }

            $remaining = (float) $invoice->remaining_amount;
            if ($amount > $remaining + 0.00001) {
                throw new InvalidArgumentException('Payment exceeds remaining invoice balance.');
            }

            Payment::query()->create([
                'customer_id' => $customer->id,
                'invoice_id' => $invoice->id,
                'amount' => (string) $amount,
                'payment_method' => $payload['payment_method'] ?? 'cash',
                'notes' => $payload['notes'] ?? null,
                'payment_date' => $payload['payment_date'],
                'created_by' => $user->id,
            ]);

            $invoice->paid_amount = (string) round((float) $invoice->paid_amount + $amount, 2);
            $invoice->syncPaymentFields();
            $invoice->save();

            $customer->recalculateDebt();

            return $invoice->fresh(['customer', 'payments']);
        });
    }
}
