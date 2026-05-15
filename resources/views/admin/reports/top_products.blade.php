@extends('layouts.admin')

@section('title', __('admin.reports.top_title'))

@section('content')
<h1 class="h3 mb-3">{{ __('admin.reports.top_title') }}</h1>
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
        <label class="form-label small mb-0">{{ __('admin.common.limit') }}</label>
        <input type="number" name="limit" value="{{ $limit }}" min="5" max="50" class="form-control" style="width:5rem">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">{{ __('admin.common.run') }}</button>
    </div>
    <div class="col-auto">
        <a class="btn btn-outline-primary" href="{{ route('admin.reports.top-products.csv', ['from' => $from, 'to' => $to, 'limit' => $limit]) }}">{{ __('admin.common.export_csv') }}</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>{{ __('admin.reports.th_sku') }}</th><th>{{ __('admin.reports.th_product') }}</th><th class="text-end">{{ __('admin.reports.th_qty_sold') }}</th><th class="text-end">{{ __('admin.reports.th_revenue') }}</th></tr></thead>
            <tbody>
            @foreach($rows as $row)
                @php $p = $products->get($row->product_id); @endphp
                <tr>
                    <td>{{ $p ? $p->sku : $row->product_id }}</td>
                    <td>{{ $p ? $p->name : __('admin.common.em_dash') }}</td>
                    <td class="text-end">{{ $row->qty_sum }}</td>
                    <td class="text-end">{{ number_format($row->revenue_sum, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
