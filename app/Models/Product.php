<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'description',
        'wholesale_price',
        'retail_price',
        'purchase_price',
        'stock_quantity',
        'minimum_stock_alert',
        'image_path',
        'barcode',
        'status',
    ];

    protected $casts = [
        'wholesale_price' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'minimum_stock_alert');
    }

    public function isLowStock()
    {
        return (int) $this->stock_quantity <= (int) $this->minimum_stock_alert;
    }

    public function unitPriceForPricingType($pricingType)
    {
        if ($pricingType === Invoice::PRICING_WHOLESALE) {
            return $this->wholesale_price;
        }

        return $this->retail_price;
    }
}
