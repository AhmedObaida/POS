@extends('layouts.admin')

@section('title', __('admin.invoices.new_title'))

@section('content')
<h1 class="h3 mb-3">{{ __('admin.invoices.new_title') }} <span class="text-muted small">{{ __('admin.invoices.subtitle_pricing') }}</span></h1>

<form method="post" action="{{ route('admin.invoices.store') }}" id="invoiceForm">
    @csrf
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.invoices.customer') }}</label>
            @php
                $customerLabel = $selectedCustomer
                    ? $selectedCustomer->name . ($selectedCustomer->phone ? ' (' . $selectedCustomer->phone . ')' : '')
                    : '';
            @endphp
            <x-entity-picker
                type="customer"
                name="customer_id"
                :search-url="route('admin.customers.search')"
                :placeholder="__('admin.common.search_customer')"
                :empty-label="__('admin.common.no_results')"
                :initial-id="old('customer_id', optional($selectedCustomer)->id)"
                :initial-label="$customerLabel"
                :required="true"
                input-class="form-control @error('customer_id') is-invalid @enderror"
            />
            @error('customer_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.invoices.pricing') }}</label>
            <select name="pricing_type" class="form-select @error('pricing_type') is-invalid @enderror" required>
                <option value="retail" {{ old('pricing_type', 'retail') === 'retail' ? 'selected' : '' }}>{{ __('admin.invoices.pricing_retail') }}</option>
                <option value="wholesale" {{ old('pricing_type') === 'wholesale' ? 'selected' : '' }}>{{ __('admin.invoices.pricing_wholesale') }}</option>
            </select>
            @error('pricing_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.invoices.discount') }}</label>
            <input type="number" step="0.01" name="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount', 0) }}">
            @error('discount_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.invoices.tax_percent') }}</label>
            <input type="number" step="0.01" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" value="{{ old('tax_rate') }}" placeholder="{{ __('admin.common.optional') }}">
            @error('tax_rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.invoices.paid_now') }}</label>
            <input type="number" step="0.01" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount', 0) }}">
            @error('paid_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('admin.common.notes') }}</label>
            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="1">{{ old('notes') }}</textarea>
            @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>

    @error('items')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.invoices.line_items') }}</span>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addLine">{{ __('admin.invoices.add_line') }}</button>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>{{ __('admin.invoices.product') }}</th><th style="width:120px">{{ __('admin.invoices.qty') }}</th><th></th></tr></thead>
                <tbody id="linesBody">
                    <tr class="line-row">
                        <td>
                            <x-entity-picker
                                type="product"
                                name="items[0][product_id]"
                                :search-url="route('admin.products.search')"
                                :placeholder="__('admin.common.search_product')"
                                :empty-label="__('admin.common.no_results')"
                                :stock-label="__('admin.invoices.stock_label')"
                                input-class="form-control form-control-sm"
                                :required="true"
                            />
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

    <button type="submit" class="btn btn-primary">{{ __('admin.invoices.create') }}</button>
    <a href="{{ route('admin.invoices.index') }}" class="btn btn-link">{{ __('admin.common.cancel') }}</a>
</form>

<template id="lineTemplate">
    <tr class="line-row">
        <td class="product-picker-cell"></td>
        <td>
            <input type="number" class="form-control form-control-sm qty-input" value="1" min="1" required>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove">{{ __('admin.invoices.remove') }}</button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
(function () {
    var lineIndex = 1;
    var tbody = document.getElementById('linesBody');
    var tpl = document.getElementById('lineTemplate');
    var productSearchUrl = @json(route('admin.products.search'));
    var productPlaceholder = @json(__('admin.common.search_product'));
    var productEmpty = @json(__('admin.common.no_results'));
    var stockLabel = @json(__('admin.invoices.stock_label'));

    function buildProductPickerHtml(name) {
        return '<div class="entity-picker position-relative" data-type="product" data-search-url="' + productSearchUrl + '" data-hidden-name="' + name + '" data-placeholder="' + productPlaceholder + '" data-empty-label="' + productEmpty + '" data-stock-label="' + stockLabel + '" data-min-length="1">' +
            '<input type="hidden" name="' + name + '" required>' +
            '<input type="text" class="entity-picker-input form-control form-control-sm" placeholder="' + productPlaceholder + '" autocomplete="off">' +
            '<small class="entity-picker-hint text-muted d-none d-block mt-1"></small>' +
            '<div class="entity-picker-results list-group position-absolute w-100 shadow-sm d-none" style="z-index:1050;max-height:220px;overflow-y:auto;"></div>' +
            '</div>';
    }

    document.getElementById('addLine').addEventListener('click', function () {
        var node = document.importNode(tpl.content, true);
        var idx = lineIndex++;
        var pickerCell = node.querySelector('.product-picker-cell');
        pickerCell.innerHTML = buildProductPickerHtml('items[' + idx + '][product_id]');
        node.querySelector('.qty-input').setAttribute('name', 'items[' + idx + '][quantity]');
        tbody.appendChild(node);
        if (window.initEntityPicker) {
            window.initEntityPicker(pickerCell.querySelector('.entity-picker'));
        }
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
