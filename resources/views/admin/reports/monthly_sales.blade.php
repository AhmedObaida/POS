@extends('layouts.admin')

@section('title', 'Monthly sales report')

@section('content')
<h1 class="h3 mb-3">Monthly sales</h1>
<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-auto">
        <label class="form-label small mb-0">Month</label>
        <input type="number" name="month" value="{{ $month }}" min="1" max="12" class="form-control" style="width:5rem">
    </div>
    <div class="col-auto">
        <label class="form-label small mb-0">Year</label>
        <input type="number" name="year" value="{{ $year }}" class="form-control" style="width:6rem">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">Run</button>
    </div>
</form>

<p class="fw-semibold">Month total: {{ number_format($monthTotal, 2) }}</p>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Day</th><th class="text-end">Invoices</th><th class="text-end">Sales</th></tr></thead>
            <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row->d }}</td>
                    <td class="text-end">{{ $row->c }}</td>
                    <td class="text-end">{{ number_format($row->total_sum, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
