@extends('layouts.admin')

@section('title', __('admin.payments.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ __('admin.payments.title') }}</h1>
    <a href="{{ route('admin.payments.create') }}" class="btn btn-primary">{{ __('admin.payments.new') }}</a>
</div>

<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label small mb-0">{{ __('admin.payments.filter_customer') }}</label>
        <select name="customer_id" class="form-select">
            <option value="">{{ __('admin.common.all') }}</option>
            @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ (string) request('customer_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label small mb-0">{{ __('admin.common.from') }}</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
    </div>
    <div class="col-md-2">
        <label class="form-label small mb-0">{{ __('admin.common.to') }}</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
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
                <th>{{ __('admin.common.date') }}</th>
                <th>{{ __('admin.payments.th_customer') }}</th>
                <th>{{ __('admin.payments.th_invoice') }}</th>
                <th class="text-end">{{ __('admin.payments.th_amount') }}</th>
                <th>{{ __('admin.payments.th_method') }}</th>
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
                            {{ __('admin.common.em_dash') }}
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($pay->amount, 2) }}</td>
                    <td>{{ __('admin.payments.method_'.$pay->payment_method) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $payments->links() }}</div>
</div>
@endsection
