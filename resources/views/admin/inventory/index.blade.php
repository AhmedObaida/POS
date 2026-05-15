@extends('layouts.admin')

@section('title', 'Inventory movements')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Inventory movements</h1>
    <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">Restock / adjust</a>
</div>

<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label small mb-0">Product</label>
        <select name="product_id" class="form-select">
            <option value="">All</option>
            @foreach($products as $p)
                <option value="{{ $p->id }}" {{ (string) request('product_id') === (string) $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label small mb-0">Type</label>
        <select name="type" class="form-select">
            <option value="">All</option>
            <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>sale</option>
            <option value="restock" {{ request('type') === 'restock' ? 'selected' : '' }}>restock</option>
            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>adjustment</option>
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">Filter</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Type</th>
                <th class="text-end">Qty delta</th>
                <th>Notes</th>
            </tr>
            </thead>
            <tbody>
            @foreach($movements as $m)
                <tr>
                    <td class="small">{{ $m->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ optional($m->product)->name }}</td>
                    <td><span class="badge bg-secondary">{{ $m->type }}</span></td>
                    <td class="text-end">{{ $m->quantity }}</td>
                    <td class="small text-muted">{{ $m->notes }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $movements->links() }}</div>
</div>
@endsection
