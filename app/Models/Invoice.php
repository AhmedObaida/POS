<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public const PRICING_RETAIL = 'retail';
    public const PRICING_WHOLESALE = 'wholesale';

    public const STATUS_PAID = 'paid';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_UNPAID = 'unpaid';

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'pricing_type',
        'subtotal',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'total',
        'paid_amount',
        'remaining_amount',
        'payment_status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function computePaymentStatus($total, $paid)
    {
        $total = (float) $total;
        $paid = (float) $paid;
        if ($paid <= 0) {
            return self::STATUS_UNPAID;
        }
        if ($paid + 0.00001 >= $total) {
            return self::STATUS_PAID;
        }

        return self::STATUS_PARTIALLY_PAID;
    }

    public function syncPaymentFields()
    {
        $total = (float) $this->total;
        $paid = max(0, min((float) $this->paid_amount, $total));
        $this->paid_amount = (string) $paid;
        $this->remaining_amount = (string) max(0, round($total - $paid, 2));
        $this->payment_status = self::computePaymentStatus($total, $paid);
    }
}
