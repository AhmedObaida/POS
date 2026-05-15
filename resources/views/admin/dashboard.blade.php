@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Dashboard</h1>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Sales today</div>
                <div class="fs-4 fw-semibold">{{ number_format($totalSalesToday, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Profit today</div>
                <div class="fs-4 fw-semibold">{{ number_format($profitToday, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Pending debts</div>
                <div class="fs-4 fw-semibold">{{ number_format($pendingDebts, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Products</div>
                <div class="fs-4 fw-semibold">{{ $totalProducts }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Low stock</div>
                <div class="fs-4 fw-semibold text-danger">{{ $lowStockCount }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Low stock products</span>
                <a href="{{ route('admin.products.index', ['low_stock' => 1]) }}" class="small">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>SKU</th><th>Name</th><th>Stock</th><th>Min</th></tr></thead>
                    <tbody>
                    @forelse($lowStockProducts as $p)
                        <tr>
                            <td><a href="{{ route('admin.products.edit', $p) }}">{{ $p->sku }}</a></td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->stock_quantity }}</td>
                            <td>{{ $p->minimum_stock_alert }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No low stock items</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm mb-3">
            <div class="card-header">Latest invoices</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($latestInvoices as $inv)
                        <tr>
                            <td><a href="{{ route('admin.invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                            <td>{{ $inv->customer->name ?? '—' }}</td>
                            <td>{{ number_format($inv->total, 2) }}</td>
                            <td><span class="badge bg-secondary">{{ $inv->payment_status }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header">Latest payments</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Customer</th><th>Amount</th><th>Invoice</th></tr></thead>
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
