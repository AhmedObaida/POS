<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'notes',
        'total_debt',
    ];

    protected $casts = [
        'total_debt' => 'decimal:2',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function recalculateDebt()
    {
        $sum = (string) $this->invoices()->sum('remaining_amount');
        $this->total_debt = $sum;
        $this->saveQuietly();
    }
}
