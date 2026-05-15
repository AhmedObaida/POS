@extends('layouts.admin')

@section('title', 'Restock / adjustment')

@section('content')
<h1 class="h3 mb-3">Restock or manual adjustment</h1>
<div class="card shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form method="post" action="{{ route('admin.inventory.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" id="mov_type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="restock" {{ old('type', 'restock') === 'restock' ? 'selected' : '' }}>Restock (add stock)</option>
                    <option value="adjustment" {{ old('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Product</label>
                <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">— Select —</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ (string) old('product_id') === (string) $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->sku }}) — stock {{ $p->stock_quantity }}</option>
                    @endforeach
                </select>
                @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3" id="dir_wrap">
                <label class="form-label">Adjustment direction</label>
                <select name="adjustment_direction" class="form-select">
                    <option value="add" {{ old('adjustment_direction', 'add') === 'add' ? 'selected' : '' }}>Add to stock</option>
                    <option value="subtract" {{ old('adjustment_direction') === 'subtract' ? 'selected' : '' }}>Subtract from stock</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-link">Cancel</a>
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
