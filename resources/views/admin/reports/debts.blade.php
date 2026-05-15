@extends('layouts.admin')

@section('title', __('admin.reports.debts_title'))

@section('content')
<h1 class="h3 mb-3">{{ __('admin.reports.debts_title') }}</h1>
<p><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.reports.debts.csv') }}">{{ __('admin.common.export_csv') }}</a></p>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>{{ __('admin.invoices.customer') }}</th><th>{{ __('admin.customers.phone') }}</th><th class="text-end">{{ __('admin.customers.total_debt') }}</th><th></th></tr></thead>
            <tbody>
            @foreach($customers as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->phone }}</td>
                    <td class="text-end fw-semibold">{{ number_format($c->total_debt, 2) }}</td>
                    <td><a href="{{ route('admin.customers.show', $c) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.reports.profile') }}</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $customers->links() }}</div>
</div>
@endsection
