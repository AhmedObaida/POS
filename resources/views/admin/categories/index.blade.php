@extends('layouts.admin')

@section('title', __('admin.categories.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ __('admin.categories.title') }}</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">{{ __('admin.categories.new') }}</a>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('admin.categories.search_name') }}">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">{{ __('admin.common.filter') }}</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>{{ __('admin.common.name') }}</th><th>{{ __('admin.common.description') }}</th><th></th></tr></thead>
            <tbody>
            @foreach($categories as $cat)
                <tr>
                    <td>{{ $cat->name }}</td>
                    <td class="text-muted small">{{ \Illuminate\Support\Str::limit($cat->description, 80) }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.common.edit') }}</a>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="post" class="d-inline" onsubmit="return confirm({{ json_encode(__('admin.categories.confirm_delete')) }});">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('admin.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $categories->links() }}</div>
</div>
@endsection
