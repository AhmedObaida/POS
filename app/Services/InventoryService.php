<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    public function restock(int $productId, int $quantity, $notes = null)
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Restock quantity must be positive.');
        }

        return DB::transaction(function () use ($productId, $quantity, $notes) {
            $product = Product::query()->lockForUpdate()->findOrFail($productId);
            $product->stock_quantity = (int) $product->stock_quantity + $quantity;
            $product->save();

            InventoryMovement::query()->create([
                'product_id' => $product->id,
                'type' => InventoryMovement::TYPE_RESTOCK,
                'quantity' => $quantity,
                'reference_type' => null,
                'reference_id' => null,
                'notes' => $notes,
            ]);

            return $product->fresh();
        });
    }

    public function adjust(int $productId, int $delta, $notes = null)
    {
        if ($delta === 0) {
            throw new InvalidArgumentException('Adjustment cannot be zero.');
        }

        return DB::transaction(function () use ($productId, $delta, $notes) {
            $product = Product::query()->lockForUpdate()->findOrFail($productId);
            $newStock = (int) $product->stock_quantity + $delta;
            if ($newStock < 0) {
                throw new InvalidArgumentException('Adjustment would result in negative stock.');
            }
            $product->stock_quantity = $newStock;
            $product->save();

            InventoryMovement::query()->create([
                'product_id' => $product->id,
                'type' => InventoryMovement::TYPE_ADJUSTMENT,
                'quantity' => $delta,
                'reference_type' => null,
                'reference_id' => null,
                'notes' => $notes,
            ]);

            return $product->fresh();
        });
    }
}
