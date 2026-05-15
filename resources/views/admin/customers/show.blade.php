@extends('layouts.admin')

@section('title', $customer->name)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0">{{ $customer->name }}</h1>
        <div class="text-muted small">{{ $customer->phone }} · {{ $customer->address }}</div>
    </div>
    <div>
        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-outline-primary">{{ __('admin.common.edit') }}</a>
        <a href="{{ route('admin.invoices.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary">{{ __('admin.customers.new_invoice') }}</a>
        <a href="{{ route('admin.payments.create', ['customer_id' => $customer->id]) }}" class="btn btn-success">{{ __('admin.customers.record_payment') }}</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">{{ __('admin.customers.cached_debt') }}</div>
                <div class="fs-3 fw-bold">{{ number_format($customer->total_debt, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">{{ __('admin.customers.sum_remainings') }}</div>
                <div class="fs-3 fw-bold">{{ number_format($remainingFromInvoices, 2) }}</div>
            </div>
        </div>
    </div>
</div>

@if($customer->notes)
    <div class="alert alert-secondary">{{ $customer->notes }}</div>
@endif

<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-inv">{{ __('admin.customers.tab_invoices') }}</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-pay">{{ __('admin.customers.tab_payments') }}</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-inv">
        <div class="table-responsive card shadow-sm">
            <table class="table mb-0">
                <thead><tr><th>{{ __('admin.invoices.th_invoice') }}</th><th>{{ __('admin.common.date') }}</th><th class="text-end">{{ __('admin.common.total') }}</th><th class="text-end">{{ __('admin.customers.th_remaining') }}</th><th>{{ __('admin.common.status') }}</th></tr></thead>
                <tbody>
                @foreach($customer->invoices as $inv)
                    <tr>
                        <td><a href="{{ route('admin.invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                        <td>{{ $inv->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-end">{{ number_format($inv->total, 2) }}</td>
                        <td class="text-end">{{ number_format($inv->remaining_amount, 2) }}</td>
                        <td><span class="badge bg-secondary">{{ __('admin.invoices.payment_'.$inv->payment_status) }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-pay">
        <div class="table-responsive card shadow-sm">
            <table class="table mb-0">
                <thead><tr><th>{{ __('admin.common.date') }}</th><th class="text-end">{{ __('admin.payments.amount') }}</th><th>{{ __('admin.payments.payment_method') }}</th><th>{{ __('admin.dashboard.th_invoice') }}</th></tr></thead>
                <tbody>
                @foreach($customer->payments as $pay)
                    <tr>
                        <td>{{ optional($pay->payment_date)->format('Y-m-d') }}</td>
                        <td class="text-end">{{ number_format($pay->amount, 2) }}</td>
                        <td>{{ $pay->payment_method }}</td>
                        <td>
                            @if($pay->invoice)
                                <a href="{{ route('admin.invoices.show', $pay->invoice) }}">{{ $pay->invoice->invoice_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
