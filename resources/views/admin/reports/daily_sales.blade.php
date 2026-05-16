@extends('layouts.admin')

@section('title', __('admin.reports.daily_title'))

@section('content')
<h1 class="h3 mb-3">{{ __('admin.reports.daily_title') }}</h1>
<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-auto">
        <label class="form-label small mb-0">{{ __('admin.reports.date') }}</label>
        <input type="date" name="date" value="{{ $date }}" class="form-control">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">{{ __('admin.common.run') }}</button>
    </div>
    <div class="col-auto">
        <a class="btn btn-outline-primary" href="{{ route('admin.reports.daily-sales.csv', ['date' => $date]) }}">{{ __('admin.common.export_csv') }}</a>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">{{ __('admin.reports.invoices_count') }}</div><div class="fs-4">{{ $totals['count'] }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">{{ __('admin.reports.sales_total') }}</div><div class="fs-4">{{ number_format($totals['sales'], 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">{{ __('admin.reports.paid_sum') }}</div><div class="fs-4">{{ number_format($totals['paid'], 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">{{ __('admin.reports.remaining_sum') }}</div><div class="fs-4">{{ number_format($totals['remaining'], 2) }}</div></div></div></div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>{{ __('admin.invoices.th_invoice') }}</th><th>{{ __('admin.invoices.customer') }}</th><th class="text-end">{{ __('admin.common.total') }}</th><th>{{ __('admin.common.status') }}</th></tr></thead>
            <tbody>
            @foreach($invoices as $inv)
                <tr>
                    <td><a href="{{ route('admin.invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                    <td>{{ optional($inv->customer)->name }}</td>
                    <td class="text-end">{{ number_format($inv->total, 2) }}</td>
                    <td><span class="badge bg-secondary">{{ __('admin.invoices.payment_'.$inv->payment_status) }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
        <div class="card-body border-top">{{ $invoices->links() }}</div>
    @endif
</div>
@endsection
