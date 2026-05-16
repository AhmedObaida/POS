@props([
    'type' => 'customer',
    'name',
    'searchUrl',
    'placeholder' => '',
    'initialId' => null,
    'initialLabel' => '',
    'required' => false,
    'inputClass' => 'form-control',
    'stockLabel' => null,
    'emptyLabel' => null,
    'minLength' => 1,
])
<div class="entity-picker position-relative"
     data-type="{{ $type }}"
     data-search-url="{{ $searchUrl }}"
     data-hidden-name="{{ $name }}"
     data-initial-id="{{ $initialId ?? '' }}"
     data-initial-label="{{ $initialLabel }}"
     data-placeholder="{{ $placeholder }}"
     @if($stockLabel) data-stock-label="{{ $stockLabel }}" @endif
     @if($emptyLabel) data-empty-label="{{ $emptyLabel }}" @endif
     data-min-length="{{ $minLength }}">
    <input type="hidden" value="{{ $initialId ?? '' }}" @if($required) required @endif>
    <input type="text"
           class="entity-picker-input {{ $inputClass }}"
           placeholder="{{ $placeholder }}"
           autocomplete="off"
           value="{{ $initialLabel }}">
    @if($type === 'product')
        <small class="entity-picker-hint text-muted d-none d-block mt-1"></small>
    @endif
    <div class="entity-picker-results list-group position-absolute w-100 shadow-sm d-none" style="z-index:1050;max-height:220px;overflow-y:auto;"></div>
</div>
<style>
.entity-picker-results.show { display: block !important; }
</style>
