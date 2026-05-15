@extends('layouts.admin')

@section('title', __('admin.customers.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ __('admin.customers.title') }}</h1>
    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">{{ __('admin.customers.new') }}</a>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('admin.customers.search_placeholder') }}">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">{{ __('admin.common.search') }}</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>{{ __('admin.common.name') }}</th><th>{{ __('admin.customers.phone') }}</th><th class="text-end">{{ __('admin.customers.total_debt') }}</th><th></th></tr></thead>
            <tbody>
            @foreach($customers as $c)
                <tr>
                    <td><a href="{{ route('admin.customers.show', $c) }}">{{ $c->name }}</a></td>
                    <td>{{ $c->phone }}</td>
                    <td class="text-end">{{ number_format($c->total_debt, 2) }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.customers.edit', $c) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.common.edit') }}</a>
                        <form action="{{ route('admin.customers.destroy', $c) }}" method="post" class="d-inline" onsubmit="return confirm({{ json_encode(__('admin.customers.confirm_delete')) }});">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('admin.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $customers->links() }}</div>
</div>
@endsection
