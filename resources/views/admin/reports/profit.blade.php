@extends('layouts.admin')

@section('title', 'Profit report')

@section('content')
<h1 class="h3 mb-3">Profit report</h1>
<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-auto">
        <label class="form-label small mb-0">From</label>
        <input type="date" name="from" value="{{ $from }}" class="form-control">
    </div>
    <div class="col-auto">
        <label class="form-label small mb-0">To</label>
        <input type="date" name="to" value="{{ $to }}" class="form-control">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">Run</button>
    </div>
    <div class="col-auto">
        <a class="btn btn-outline-primary" href="{{ route('admin.reports.profit.pdf', ['from' => $from, 'to' => $to]) }}">Export PDF</a>
    </div>
</form>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Revenue (invoice totals)</div>
            <div class="fs-3 fw-bold">{{ number_format($revenue, 2) }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Gross profit (excl. operating costs)</div>
            <div class="fs-3 fw-bold text-success">{{ number_format($profit, 2) }}</div>
        </div></div>
    </div>
</div>
@endsection
