@extends('layouts.admin')

@section('title', __('admin.inventory.create_title'))

@section('content')
<h1 class="h3 mb-3">{{ __('admin.inventory.create_title') }}</h1>
<div class="card shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form method="post" action="{{ route('admin.inventory.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ __('admin.inventory.type') }}</label>
                <select name="type" id="mov_type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="restock" {{ old('type', 'restock') === 'restock' ? 'selected' : '' }}>{{ __('admin.inventory.type_restock') }}</option>
                    <option value="adjustment" {{ old('type') === 'adjustment' ? 'selected' : '' }}>{{ __('admin.inventory.type_adjustment') }}</option>
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.inventory.product') }}</label>
                @php
                    $productLabel = $selectedProduct
                        ? $selectedProduct->name . ' (' . $selectedProduct->sku . ') — ' . __('admin.invoices.stock_label') . ' ' . $selectedProduct->stock_quantity
                        : '';
                @endphp
                <x-entity-picker
                    type="product"
                    name="product_id"
                    :search-url="route('admin.products.search')"
                    :placeholder="__('admin.common.search_product')"
                    :empty-label="__('admin.common.no_results')"
                    :stock-label="__('admin.invoices.stock_label')"
                    :initial-id="old('product_id', optional($selectedProduct)->id)"
                    :initial-label="$productLabel"
                    :required="true"
                    input-class="form-control @error('product_id') is-invalid @enderror"
                />
                @error('product_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.inventory.quantity') }}</label>
                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3" id="dir_wrap">
                <label class="form-label">{{ __('admin.inventory.adjust_direction') }}</label>
                <select name="adjustment_direction" class="form-select">
                    <option value="add" {{ old('adjustment_direction', 'add') === 'add' ? 'selected' : '' }}>{{ __('admin.inventory.dir_add') }}</option>
                    <option value="subtract" {{ old('adjustment_direction') === 'subtract' ? 'selected' : '' }}>{{ __('admin.inventory.dir_subtract') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.notes') }}</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">{{ __('admin.inventory.submit') }}</button>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-link">{{ __('admin.common.cancel') }}</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var sel = document.getElementById('mov_type');
    var wrap = document.getElementById('dir_wrap');
    function toggle() {
        wrap.style.display = sel.value === 'adjustment' ? 'block' : 'none';
    }
    sel.addEventListener('change', toggle);
    toggle();
})();
</script>
@endpush
@endsection
