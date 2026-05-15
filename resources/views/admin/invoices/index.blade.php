@extends('layouts.admin')

@section('title', 'Invoices')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Invoices</h1>
    <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">New invoice (POS)</a>
</div>

<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-md-3">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Invoice #">
    </div>
    <div class="col-md-2">
        <select name="payment_status" class="form-select">
            <option value="">All statuses</option>
            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>paid</option>
            <option value="partially_paid" {{ request('payment_status') === 'partially_paid' ? 'selected' : '' }}>partially_paid</option>
            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>unpaid</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="customer_id" class="form-select">
            <option value="">All customers</option>
            @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ (string) request('customer_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">Filter</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Pricing</th>
                <th class="text-end">Total</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Remaining</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoices as $inv)
                <tr>
                    <td><a href="{{ route('admin.invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                    <td>{{ optional($inv->customer)->name }}</td>
                    <td>{{ $inv->pricing_type }}</td>
                    <td class="text-end">{{ number_format($inv->total, 2) }}</td>
                    <td class="text-end">{{ number_format($inv->paid_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($inv->remaining_amount, 2) }}</td>
                    <td><span class="badge bg-secondary">{{ $inv->payment_status }}</span></td>
                    <td class="small">{{ $inv->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $invoices->links() }}</div>
</div>
@endsection
