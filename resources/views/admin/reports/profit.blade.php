@extends('layouts.admin')

@section('title', __('admin.reports.profit_title'))

@section('content')
<h1 class="h3 mb-3">{{ __('admin.reports.profit_title') }}</h1>
<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-auto">
        <label class="form-label small mb-0">{{ __('admin.common.from') }}</label>
        <input type="date" name="from" value="{{ $from }}" class="form-control">
    </div>
    <div class="col-auto">
        <label class="form-label small mb-0">{{ __('admin.common.to') }}</label>
        <input type="date" name="to" value="{{ $to }}" class="form-control">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">{{ __('admin.common.run') }}</button>
    </div>
    <div class="col-auto">
        <a class="btn btn-outline-primary" href="{{ route('admin.reports.profit.pdf', ['from' => $from, 'to' => $to]) }}">{{ __('admin.common.export_pdf') }}</a>
    </div>
</form>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">{{ __('admin.reports.revenue') }}</div>
            <div class="fs-3 fw-bold">{{ number_format($revenue, 2) }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">{{ __('admin.reports.gross_profit') }}</div>
            <div class="fs-3 fw-bold text-success">{{ number_format($profit, 2) }}</div>
        </div></div>
    </div>
</div>
@endsection
