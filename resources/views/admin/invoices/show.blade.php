@extends('layouts.admin')

@section('title', $invoice->invoice_number)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0">{{ $invoice->invoice_number }}</h1>
        <div class="text-muted small">{{ $invoice->created_at->format('Y-m-d H:i') }} · {{ optional($invoice->creator)->name }}</div>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.invoices.print', $invoice) }}" class="btn btn-outline-secondary" target="_blank">Print</a>
        <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-outline-secondary">PDF</a>
        @if((float) $invoice->remaining_amount > 0)
            <a href="{{ route('admin.payments.create', ['customer_id' => $invoice->customer_id, 'invoice_id' => $invoice->id]) }}" class="btn btn-success">Pay balance</a>
        @endif
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Customer</div>
            <div class="fw-semibold"><a href="{{ route('admin.customers.show', $invoice->customer) }}">{{ $invoice->customer->name }}</a></div>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Pricing</div>
            <div>{{ $invoice->pricing_type }}</div>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Total</div>
            <div class="fw-bold">{{ number_format($invoice->total, 2) }}</div>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Paid</div>
            <div>{{ number_format($invoice->paid_amount, 2) }}</div>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Remaining</div>
            <div class="fw-bold text-danger">{{ number_format($invoice->remaining_amount, 2) }}</div>
        </div></div>
    </div>
    <div class="col-md-1">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Status</div>
            <div><span class="badge bg-secondary">{{ $invoice->payment_status }}</span></div>
        </div></div>
    </div>
</div>

@if($invoice->notes)
    <div class="alert alert-light border">{{ $invoice->notes }}</div>
@endif

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
            <tr>
                <th>Product</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Unit</th>
                <th class="text-end">Line</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoice->items as $line)
                <tr>
                    <td>{{ $line->product->name ?? '—' }} <span class="text-muted small">({{ optional($line->product)->sku }})</span></td>
                    <td class="text-end">{{ $line->quantity }}</td>
                    <td class="text-end">{{ number_format($line->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($line->line_total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="table-light">
            <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end">{{ number_format($invoice->subtotal, 2) }}</td></tr>
            <tr><td colspan="3" class="text-end">Discount</td><td class="text-end">-{{ number_format($invoice->discount_amount, 2) }}</td></tr>
            <tr><td colspan="3" class="text-end">Tax</td><td class="text-end">{{ number_format($invoice->tax_amount, 2) }}</td></tr>
            <tr><td colspan="3" class="text-end fw-bold">Total</td><td class="text-end fw-bold">{{ number_format($invoice->total, 2) }}</td></tr>
            </tfoot>
        </table>
    </div>
</div>

@if($invoice->payments->count())
    <h2 class="h5 mt-4">Payments on this invoice</h2>
    <div class="table-responsive card shadow-sm">
        <table class="table mb-0">
            <thead><tr><th>Date</th><th class="text-end">Amount</th><th>Method</th></tr></thead>
            <tbody>
            @foreach($invoice->payments as $pay)
                <tr>
                    <td>{{ optional($pay->payment_date)->format('Y-m-d') }}</td>
                    <td class="text-end">{{ number_format($pay->amount, 2) }}</td>
                    <td>{{ $pay->payment_method }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

<a href="{{ route('admin.invoices.index') }}" class="btn btn-link mt-3">Back to list</a>
@endsection
