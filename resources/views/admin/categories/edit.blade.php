@extends('layouts.admin')

@section('title', __('admin.categories.edit'))

@section('content')
<h1 class="h3 mb-3">{{ __('admin.categories.edit') }}</h1>
<div class="card shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form method="post" action="{{ route('admin.categories.update', $category) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.description') }}</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $category->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-primary" type="submit">{{ __('admin.common.update') }}</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-link">{{ __('admin.common.cancel') }}</a>
        </form>
    </div>
</div>
@endsection
