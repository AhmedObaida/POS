@extends('layouts.admin')

@section('title', __('admin.customers.new'))

@section('content')
<h1 class="h3 mb-3">{{ __('admin.customers.new') }}</h1>
<div class="card shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form method="post" action="{{ route('admin.customers.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.customers.phone') }}</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.customers.address') }}</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.notes') }}</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-primary" type="submit">{{ __('admin.common.save') }}</button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-link">{{ __('admin.common.cancel') }}</a>
        </form>
    </div>
</div>
@endsection
