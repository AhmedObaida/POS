@extends('layouts.admin')

@section('title', 'New invoice')

@section('content')
<h1 class="h3 mb-3">New invoice <span class="text-muted small">(Retail / Wholesale)</span></h1>

<form method="post" action="{{ route('admin.invoices.store') }}" id="invoiceForm">
    @csrf
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Customer</label>
            <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                <option value="">— Select —</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ (string) old('customer_id', request('customer_id')) === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            @error('customer_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Pricing</label>
            <select name="pricing_type" class="form-select @error('pricing_type') is-invalid @enderror" required>
                <option value="retail" {{ old('pricing_type', 'retail') === 'retail' ? 'selected' : '' }}>Retail (قطاعي)</option>
                <option value="wholesale" {{ old('pricing_type') === 'wholesale' ? 'selected' : '' }}>Wholesale (جملة)</option>
            </select>
            @error('pricing_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label">Discount</label>
            <input type="number" step="0.01" name="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount', 0) }}">
            @error('discount_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label">Tax %</label>
            <input type="number" step="0.01" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" value="{{ old('tax_rate') }}" placeholder="optional">
            @error('tax_rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label">Paid now</label>
            <input type="number" step="0.01" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount', 0) }}">
            @error('paid_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="1">{{ old('notes') }}</textarea>
            @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>

    @error('items')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Line items</span>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addLine">Add line</button>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Product</th><th style="width:120px">Qty</th><th></th></tr></thead>
                <tbody id="linesBody">
                    <tr class="line-row" data-line="0">
                        <td>
                            <select name="items[0][product_id]" class="form-select form-select-sm product-select @error('items.0.product_id') is-invalid @enderror" required>
                                <option value="">— Product —</option>
                                @foreach($productOptions as $p)
                                    <option value="{{ $p->id }}" data-stock="{{ $p->stock_quantity }}">
                                        {{ $p->name }} ({{ $p->sku }}) — stock {{ $p->stock_quantity }}
                                    </option>
                                @endforeach
                            </select>
                            @error('items.0.product_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </td>
                        <td>
                            <input type="number" name="items[0][quantity]" class="form-control form-control-sm @error('items.0.quantity') is-invalid @enderror" value="{{ old('items.0.quantity', 1) }}" min="1" required>
                            @error('items.0.quantity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <p class="small text-muted">Stock is validated on save. Use <a href="{{ route('admin.products.search') }}?q=" target="_blank">product search JSON</a> from POS integrations if needed.</p>

    <button type="submit" class="btn btn-primary">Create invoice</button>
    <a href="{{ route('admin.invoices.index') }}" class="btn btn-link">Cancel</a>
</form>

<template id="lineTemplate">
    <tr class="line-row">
        <td>
            <select class="form-select form-select-sm product-select" data-name-product required>
                <option value="">— Product —</option>
                @foreach($productOptions as $p)
                    <option value="{{ $p->id }}" data-stock="{{ $p->stock_quantity }}">
                        {{ $p->name }} ({{ $p->sku }}) — stock {{ $p->stock_quantity }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm qty-input" data-name-qty value="1" min="1" required>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove">Remove</button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
(function () {
    var lineIndex = 1;
    var tbody = document.getElementById('linesBody');
    var tpl = document.getElementById('lineTemplate');
    document.getElementById('addLine').addEventListener('click', function () {
        var node = document.importNode(tpl.content, true);
        var idx = lineIndex++;
        node.querySelector('.product-select').setAttribute('name', 'items[' + idx + '][product_id]');
        node.querySelector('.qty-input').setAttribute('name', 'items[' + idx + '][quantity]');
        tbody.appendChild(node);
    });
    tbody.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('btn-remove')) {
            var row = e.target.closest('tr');
            if (tbody.querySelectorAll('tr').length > 1) {
                row.parentNode.removeChild(row);
            }
        }
    });
})();
</script>
@endpush
@endsection
