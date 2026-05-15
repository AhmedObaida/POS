@extends('layouts.admin')

@section('title', 'Top products')

@section('content')
<h1 class="h3 mb-3">Top selling products</h1>
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
        <label class="form-label small mb-0">Limit</label>
        <input type="number" name="limit" value="{{ $limit }}" min="5" max="50" class="form-control" style="width:5rem">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">Run</button>
    </div>
    <div class="col-auto">
        <a class="btn btn-outline-primary" href="{{ route('admin.reports.top-products.csv', ['from' => $from, 'to' => $to, 'limit' => $limit]) }}">Export CSV</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>SKU</th><th>Product</th><th class="text-end">Qty sold</th><th class="text-end">Revenue</th></tr></thead>
            <tbody>
            @foreach($rows as $row)
                @php $p = $products->get($row->product_id); @endphp
                <tr>
                    <td>{{ $p ? $p->sku : $row->product_id }}</td>
                    <td>{{ $p ? $p->name : '—' }}</td>
                    <td class="text-end">{{ $row->qty_sum }}</td>
                    <td class="text-end">{{ number_format($row->revenue_sum, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
