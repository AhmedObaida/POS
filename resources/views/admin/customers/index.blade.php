@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Customers</h1>
    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">New customer</a>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name or phone">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Name</th><th>Phone</th><th class="text-end">Total debt</th><th></th></tr></thead>
            <tbody>
            @foreach($customers as $c)
                <tr>
                    <td><a href="{{ route('admin.customers.show', $c) }}">{{ $c->name }}</a></td>
                    <td>{{ $c->phone }}</td>
                    <td class="text-end">{{ number_format($c->total_debt, 2) }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.customers.edit', $c) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.customers.destroy', $c) }}" method="post" class="d-inline" onsubmit="return confirm('Delete customer?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
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
