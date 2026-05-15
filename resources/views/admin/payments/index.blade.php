@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Payments</h1>
    <a href="{{ route('admin.payments.create') }}" class="btn btn-primary">Record payment</a>
</div>

<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label small mb-0">Customer</label>
        <select name="customer_id" class="form-select">
            <option value="">All</option>
            @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ (string) request('customer_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label small mb-0">From</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
    </div>
    <div class="col-md-2">
        <label class="form-label small mb-0">To</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
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
                <th>Date</th>
                <th>Customer</th>
                <th>Invoice</th>
                <th class="text-end">Amount</th>
                <th>Method</th>
            </tr>
            </thead>
            <tbody>
            @foreach($payments as $pay)
                <tr>
                    <td>{{ optional($pay->payment_date)->format('Y-m-d') }}</td>
                    <td><a href="{{ route('admin.customers.show', $pay->customer) }}">{{ optional($pay->customer)->name }}</a></td>
                    <td>
                        @if($pay->invoice)
                            <a href="{{ route('admin.invoices.show', $pay->invoice) }}">{{ $pay->invoice->invoice_number }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($pay->amount, 2) }}</td>
                    <td>{{ $pay->payment_method }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $payments->links() }}</div>
</div>
@endsection
