@extends('layouts.admin')

@section('title', __('admin.categories.new'))

@section('content')
<h1 class="h3 mb-3">{{ __('admin.categories.new') }}</h1>
<div class="card shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form method="post" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.description') }}</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-primary" type="submit">{{ __('admin.common.save') }}</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-link">{{ __('admin.common.cancel') }}</a>
        </form>
    </div>
</div>
@endsection
