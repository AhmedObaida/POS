@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">New product</a>
</div>

<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label small mb-0">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, SKU, barcode">
    </div>
    <div class="col-md-2">
        <label class="form-label small mb-0">Category</label>
        <select name="category_id" class="form-select">
            <option value="">All</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ (string) request('category_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label small mb-0">Status</label>
        <select name="status" class="form-select">
            <option value="">All</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <div class="col-md-2 form-check mt-4">
        <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="low_stock" {{ request('low_stock') ? 'checked' : '' }}>
        <label class="form-check-label" for="low_stock">Low stock only</label>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary" type="submit">Filter</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-link">Reset</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
            <tr>
                <th></th>
                <th>SKU</th>
                <th>Name</th>
                <th>Category</th>
                <th class="text-end">Retail</th>
                <th class="text-end">Wholesale</th>
                <th class="text-end">Stock</th>
                <th>Status</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $p)
                <tr class="{{ $p->isLowStock() ? 'table-warning' : '' }}">
                    <td style="width:48px;">
                        @if($p->image_path)
                            <img src="{{ asset('storage/'.$p->image_path) }}" alt="" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                        @endif
                    </td>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ optional($p->category)->name }}</td>
                    <td class="text-end">{{ number_format($p->retail_price, 2) }}</td>
                    <td class="text-end">{{ number_format($p->wholesale_price, 2) }}</td>
                    <td class="text-end">{{ $p->stock_quantity }}</td>
                    <td><span class="badge bg-{{ $p->status === 'active' ? 'success' : 'secondary' }}">{{ $p->status }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.products.destroy', $p) }}" method="post" class="d-inline" onsubmit="return confirm('Delete product?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $products->links() }}</div>
</div>
@endsection
