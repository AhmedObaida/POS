<?php

namespace Tests\Unit;

use App\Models\Invoice;
use PHPUnit\Framework\TestCase;

class InvoiceModelTest extends TestCase
{
    public function test_compute_payment_status()
    {
        $this->assertSame(Invoice::STATUS_UNPAID, Invoice::computePaymentStatus(100, 0));
        $this->assertSame(Invoice::STATUS_PAID, Invoice::computePaymentStatus(100, 100));
        $this->assertSame(Invoice::STATUS_PAID, Invoice::computePaymentStatus(100, 100.00001));
        $this->assertSame(Invoice::STATUS_PARTIALLY_PAID, Invoice::computePaymentStatus(100, 40));
    }
}
