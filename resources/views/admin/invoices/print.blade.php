<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body class="p-4">
<div class="no-print mb-3">
    <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
    <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-link">Back</a>
</div>
<div class="mx-auto" style="max-width: 720px;">
    <h1 class="h4">{{ config('app.name') }}</h1>
    <p class="mb-1"><strong>Invoice:</strong> {{ $invoice->invoice_number }}</p>
    <p class="mb-1"><strong>Date:</strong> {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
    <p class="mb-1"><strong>Customer:</strong> {{ $invoice->customer->name }}</p>
    <p class="mb-3"><strong>Pricing:</strong> {{ $invoice->pricing_type }}</p>
    <table class="table table-sm">
        <thead><tr><th>Item</th><th class="text-end">Qty</th><th class="text-end">Price</th><th class="text-end">Total</th></tr></thead>
        <tbody>
        @foreach($invoice->items as $line)
            <tr>
                <td>{{ $line->product->name ?? '' }}</td>
                <td class="text-end">{{ $line->quantity }}</td>
                <td class="text-end">{{ number_format($line->unit_price, 2) }}</td>
                <td class="text-end">{{ number_format($line->line_total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p class="text-end"><strong>Total:</strong> {{ number_format($invoice->total, 2) }}</p>
    <p class="text-end"><strong>Paid:</strong> {{ number_format($invoice->paid_amount, 2) }}</p>
    <p class="text-end"><strong>Remaining:</strong> {{ number_format($invoice->remaining_amount, 2) }}</p>
    <p class="text-end"><strong>Status:</strong> {{ $invoice->payment_status }}</p>
</div>
</body>
</html>
