@extends('layouts.admin')

@section('title', $invoice->invoice_number)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0">{{ $invoice->invoice_number }}</h1>
        <div class="text-muted small">{{ $invoice->created_at->format('Y-m-d H:i') }} · {{ optional($invoice->creator)->name }}</div>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.invoices.print', $invoice) }}" class="btn btn-outline-secondary" target="_blank">{{ __('admin.common.print') }}</a>
        <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-outline-secondary">{{ __('admin.invoices.pdf') }}</a>
        @if((float) $invoice->remaining_amount > 0)
            <a href="{{ route('admin.payments.create', ['customer_id' => $invoice->customer_id, 'invoice_id' => $invoice->id]) }}" class="btn btn-success">{{ __('admin.invoices.pay_balance') }}</a>
        @endif
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">{{ __('admin.invoices.customer') }}</div>
            <div class="fw-semibold"><a href="{{ route('admin.customers.show', $invoice->customer) }}">{{ $invoice->customer->name }}</a></div>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">{{ __('admin.invoices.pricing') }}</div>
            <div>{{ __('admin.invoices.pricing_type_'.$invoice->pricing_type) }}</div>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">{{ __('admin.common.total') }}</div>
            <div class="fw-bold">{{ number_format($invoice->total, 2) }}</div>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">{{ __('admin.invoices.paid') }}</div>
            <div>{{ number_format($invoice->paid_amount, 2) }}</div>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">{{ __('admin.invoices.remaining') }}</div>
            <div class="fw-bold text-danger">{{ number_format($invoice->remaining_amount, 2) }}</div>
        </div></div>
    </div>
    <div class="col-md-1">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">{{ __('admin.common.status') }}</div>
            <div><span class="badge bg-secondary">{{ __('admin.invoices.payment_'.$invoice->payment_status) }}</span></div>
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
                <th>{{ __('admin.invoices.product') }}</th>
                <th class="text-end">{{ __('admin.invoices.qty') }}</th>
                <th class="text-end">{{ __('admin.invoices.th_unit') }}</th>
                <th class="text-end">{{ __('admin.invoices.th_line') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoice->items as $line)
                <tr>
                    <td>{{ $line->product->name ?? __('admin.common.em_dash') }} <span class="text-muted small">({{ optional($line->product)->sku }})</span></td>
                    <td class="text-end">{{ $line->quantity }}</td>
                    <td class="text-end">{{ number_format($line->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($line->line_total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="table-light">
            <tr><td colspan="3" class="text-end">{{ __('admin.invoices.subtotal') }}</td><td class="text-end">{{ number_format($invoice->subtotal, 2) }}</td></tr>
            <tr><td colspan="3" class="text-end">{{ __('admin.invoices.discount') }}</td><td class="text-end">-{{ number_format($invoice->discount_amount, 2) }}</td></tr>
            <tr><td colspan="3" class="text-end">{{ __('admin.invoices.tax') }}</td><td class="text-end">{{ number_format($invoice->tax_amount, 2) }}</td></tr>
            <tr><td colspan="3" class="text-end fw-bold">{{ __('admin.common.total') }}</td><td class="text-end fw-bold">{{ number_format($invoice->total, 2) }}</td></tr>
            </tfoot>
        </table>
    </div>
</div>

@if($invoice->payments->count())
    <h2 class="h5 mt-4">{{ __('admin.invoices.payments_on_invoice') }}</h2>
    <div class="table-responsive card shadow-sm">
        <table class="table mb-0">
            <thead><tr><th>{{ __('admin.common.date') }}</th><th class="text-end">{{ __('admin.invoices.th_amount') }}</th><th>{{ __('admin.payments.payment_method') }}</th></tr></thead>
            <tbody>
            @foreach($invoice->payments as $pay)
                <tr>
                    <td>{{ optional($pay->payment_date)->format('Y-m-d') }}</td>
                    <td class="text-end">{{ number_format($pay->amount, 2) }}</td>
                    <td>{{ __('admin.payments.method_'.$pay->payment_method) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

<a href="{{ route('admin.invoices.index') }}" class="btn btn-link mt-3">{{ __('admin.common.back_list') }}</a>
@endsection
