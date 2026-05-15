@extends('layouts.admin')

@section('title', 'Customer debts')

@section('content')
<h1 class="h3 mb-3">Customer debts</h1>
<p><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.reports.debts.csv') }}">Export CSV</a></p>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Customer</th><th>Phone</th><th class="text-end">Total debt</th><th></th></tr></thead>
            <tbody>
            @foreach($customers as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->phone }}</td>
                    <td class="text-end fw-semibold">{{ number_format($c->total_debt, 2) }}</td>
                    <td><a href="{{ route('admin.customers.show', $c) }}" class="btn btn-sm btn-outline-primary">Profile</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $customers->links() }}</div>
</div>
@endsection
