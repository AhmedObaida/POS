@extends('layouts.admin')

@section('title', 'Inventory report')

@section('content')
<h1 class="h3 mb-3">Inventory report</h1>
<form method="get" class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="low_only" value="1" id="low_only" {{ request('low_only') ? 'checked' : '' }}>
        <label class="form-check-label" for="low_only">Low stock only</label>
    </div>
    <button class="btn btn-outline-secondary btn-sm" type="submit">Filter</button>
    <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.reports.inventory.csv') }}">Export CSV</a>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>SKU</th><th>Name</th><th>Category</th><th class="text-end">Stock</th><th class="text-end">Min</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($products as $p)
                <tr class="{{ $p->isLowStock() ? 'table-warning' : '' }}">
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ optional($p->category)->name }}</td>
                    <td class="text-end">{{ $p->stock_quantity }}</td>
                    <td class="text-end">{{ $p->minimum_stock_alert }}</td>
                    <td>{{ $p->status }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $products->links() }}</div>
</div>
@endsection
