@extends('layouts.admin')

@section('title', __('admin.reports.inventory_title'))

@section('content')
<h1 class="h3 mb-3">{{ __('admin.reports.inventory_title') }}</h1>
<form method="get" class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="low_only" value="1" id="low_only" {{ request('low_only') ? 'checked' : '' }}>
        <label class="form-check-label" for="low_only">{{ __('admin.reports.low_only') }}</label>
    </div>
    <button class="btn btn-outline-secondary btn-sm" type="submit">{{ __('admin.common.filter') }}</button>
    <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.reports.inventory.csv') }}">{{ __('admin.common.export_csv') }}</a>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>{{ __('admin.reports.th_sku') }}</th><th>{{ __('admin.reports.th_name') }}</th><th>{{ __('admin.reports.th_category') }}</th><th class="text-end">{{ __('admin.reports.th_stock') }}</th><th class="text-end">{{ __('admin.reports.th_min') }}</th><th>{{ __('admin.reports.th_status') }}</th></tr></thead>
            <tbody>
            @foreach($products as $p)
                <tr class="{{ $p->isLowStock() ? 'table-warning' : '' }}">
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ optional($p->category)->name }}</td>
                    <td class="text-end">{{ $p->stock_quantity }}</td>
                    <td class="text-end">{{ $p->minimum_stock_alert }}</td>
                    <td>{{ __('admin.products.status_'.$p->status) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $products->links() }}</div>
</div>
@endsection
