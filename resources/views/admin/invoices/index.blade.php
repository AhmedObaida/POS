@extends('layouts.admin')

@section('title', __('admin.invoices.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ __('admin.invoices.title') }}</h1>
    <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">{{ __('admin.invoices.new_pos') }}</a>
</div>

<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-md-3">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('admin.invoices.search_invoice') }}">
    </div>
    <div class="col-md-2">
        <select name="payment_status" class="form-select">
            <option value="">{{ __('admin.invoices.all_statuses') }}</option>
            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>{{ __('admin.invoices.payment_paid') }}</option>
            <option value="partially_paid" {{ request('payment_status') === 'partially_paid' ? 'selected' : '' }}>{{ __('admin.invoices.payment_partially_paid') }}</option>
            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>{{ __('admin.invoices.payment_unpaid') }}</option>
        </select>
    </div>
    <div class="col-md-3">
        @php
            $filterCustomerLabel = $filterCustomer
                ? $filterCustomer->name . ($filterCustomer->phone ? ' (' . $filterCustomer->phone . ')' : '')
                : '';
        @endphp
        <x-entity-picker
            type="customer"
            name="customer_id"
            :search-url="route('admin.customers.search')"
            :placeholder="__('admin.common.search_customer')"
            :empty-label="__('admin.common.no_results')"
            :initial-id="request('customer_id')"
            :initial-label="$filterCustomerLabel"
        />
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">{{ __('admin.common.filter') }}</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>{{ __('admin.invoices.th_invoice') }}</th>
                <th>{{ __('admin.invoices.customer') }}</th>
                <th>{{ __('admin.invoices.th_pricing') }}</th>
                <th class="text-end">{{ __('admin.common.total') }}</th>
                <th class="text-end">{{ __('admin.invoices.paid') }}</th>
                <th class="text-end">{{ __('admin.invoices.remaining') }}</th>
                <th>{{ __('admin.common.status') }}</th>
                <th>{{ __('admin.common.date') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoices as $inv)
                <tr>
                    <td><a href="{{ route('admin.invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                    <td>{{ optional($inv->customer)->name }}</td>
                    <td>{{ __('admin.invoices.pricing_type_'.$inv->pricing_type) }}</td>
                    <td class="text-end">{{ number_format($inv->total, 2) }}</td>
                    <td class="text-end">{{ number_format($inv->paid_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($inv->remaining_amount, 2) }}</td>
                    <td><span class="badge bg-secondary">{{ __('admin.invoices.payment_'.$inv->payment_status) }}</span></td>
                    <td class="small">{{ $inv->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $invoices->links() }}</div>
</div>
@endsection
