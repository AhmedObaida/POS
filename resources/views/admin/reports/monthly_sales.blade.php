@extends('layouts.admin')

@section('title', __('admin.reports.monthly_title'))

@section('content')
<h1 class="h3 mb-3">{{ __('admin.reports.monthly_title') }}</h1>
<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-auto">
        <label class="form-label small mb-0">{{ __('admin.reports.month') }}</label>
        <input type="number" name="month" value="{{ $month }}" min="1" max="12" class="form-control" style="width:5rem">
    </div>
    <div class="col-auto">
        <label class="form-label small mb-0">{{ __('admin.reports.year') }}</label>
        <input type="number" name="year" value="{{ $year }}" class="form-control" style="width:6rem">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">{{ __('admin.common.run') }}</button>
    </div>
</form>

<p class="fw-semibold">{{ __('admin.reports.month_total') }}: {{ number_format($monthTotal, 2) }}</p>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>{{ __('admin.reports.day') }}</th><th class="text-end">{{ __('admin.reports.invoices_count') }}</th><th class="text-end">{{ __('admin.reports.sales_total') }}</th></tr></thead>
            <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row->d }}</td>
                    <td class="text-end">{{ $row->c }}</td>
                    <td class="text-end">{{ number_format($row->total_sum, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
