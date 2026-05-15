@extends('layouts.admin')

@section('title', 'Daily sales report')

@section('content')
<h1 class="h3 mb-3">Daily sales</h1>
<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-auto">
        <label class="form-label small mb-0">Date</label>
        <input type="date" name="date" value="{{ $date }}" class="form-control">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">Run</button>
    </div>
    <div class="col-auto">
        <a class="btn btn-outline-primary" href="{{ route('admin.reports.daily-sales.csv', ['date' => $date]) }}">Export CSV</a>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Invoices</div><div class="fs-4">{{ $totals['count'] }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Sales total</div><div class="fs-4">{{ number_format($totals['sales'], 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Paid</div><div class="fs-4">{{ number_format($totals['paid'], 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Remaining</div><div class="fs-4">{{ number_format($totals['remaining'], 2) }}</div></div></div></div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Invoice</th><th>Customer</th><th class="text-end">Total</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($invoices as $inv)
                <tr>
                    <td><a href="{{ route('admin.invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                    <td>{{ optional($inv->customer)->name }}</td>
                    <td class="text-end">{{ number_format($inv->total, 2) }}</td>
                    <td>{{ $inv->payment_status }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
