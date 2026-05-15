@extends('layouts.admin')

@section('title', __('admin.dashboard.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('admin.dashboard.title') }}</h1>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">{{ __('admin.dashboard.sales_today') }}</div>
                <div class="fs-4 fw-semibold">{{ number_format($totalSalesToday, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">{{ __('admin.dashboard.profit_today') }}</div>
                <div class="fs-4 fw-semibold">{{ number_format($profitToday, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">{{ __('admin.dashboard.pending_debts') }}</div>
                <div class="fs-4 fw-semibold">{{ number_format($pendingDebts, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">{{ __('admin.dashboard.products') }}</div>
                <div class="fs-4 fw-semibold">{{ $totalProducts }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">{{ __('admin.dashboard.low_stock') }}</div>
                <div class="fs-4 fw-semibold text-danger">{{ $lowStockCount }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ __('admin.dashboard.low_stock_products') }}</span>
                <a href="{{ route('admin.products.index', ['low_stock' => 1]) }}" class="small">{{ __('admin.dashboard.view_all') }}</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>{{ __('admin.products.sku') }}</th><th>{{ __('admin.common.name') }}</th><th>{{ __('admin.products.stock') }}</th><th>{{ __('admin.reports.th_min') }}</th></tr></thead>
                    <tbody>
                    @forelse($lowStockProducts as $p)
                        <tr>
                            <td><a href="{{ route('admin.products.edit', $p) }}">{{ $p->sku }}</a></td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->stock_quantity }}</td>
                            <td>{{ $p->minimum_stock_alert }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">{{ __('admin.dashboard.no_low_stock') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm mb-3">
            <div class="card-header">{{ __('admin.dashboard.latest_invoices') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>#</th><th>{{ __('admin.dashboard.th_customer') }}</th><th>{{ __('admin.common.total') }}</th><th>{{ __('admin.common.status') }}</th></tr></thead>
                    <tbody>
                    @foreach($latestInvoices as $inv)
                        <tr>
                            <td><a href="{{ route('admin.invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                            <td>{{ $inv->customer->name ?? '—' }}</td>
                            <td>{{ number_format($inv->total, 2) }}</td>
                            <td><span class="badge bg-secondary">{{ __('admin.invoices.payment_'.$inv->payment_status) }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header">{{ __('admin.dashboard.latest_payments') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>{{ __('admin.common.date') }}</th><th>{{ __('admin.dashboard.th_customer') }}</th><th>{{ __('admin.payments.amount') }}</th><th>{{ __('admin.dashboard.th_invoice') }}</th></tr></thead>
                    <tbody>
                    @foreach($latestPayments as $pay)
                        <tr>
                            <td>{{ optional($pay->payment_date)->format('Y-m-d') }}</td>
                            <td>{{ $pay->customer->name ?? '—' }}</td>
                            <td>{{ number_format($pay->amount, 2) }}</td>
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
</div>
@endsection
