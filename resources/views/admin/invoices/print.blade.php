@php $isRtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    @if ($isRtl)
        <link href="{{ asset('css/bootstrap-rtl.min.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @endif
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body class="p-4">
<div class="no-print mb-3">
    <button type="button" class="btn btn-primary" onclick="window.print()">{{ __('admin.print.button_print') }}</button>
    <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-link">{{ __('admin.common.back_short') }}</a>
</div>
<div class="mx-auto" style="max-width: 720px;">
    <h1 class="h4">{{ config('app.name') }}</h1>
    <p class="mb-1"><strong>{{ __('admin.print.invoice') }}:</strong> {{ $invoice->invoice_number }}</p>
    <p class="mb-1"><strong>{{ __('admin.print.date') }}:</strong> {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
    <p class="mb-1"><strong>{{ __('admin.print.customer') }}:</strong> {{ $invoice->customer->name }}</p>
    <p class="mb-3"><strong>{{ __('admin.print.pricing') }}:</strong> {{ __('admin.invoices.pricing_type_'.$invoice->pricing_type) }}</p>
    <table class="table table-sm">
        <thead><tr><th>{{ __('admin.print.item') }}</th><th class="text-end">{{ __('admin.print.qty') }}</th><th class="text-end">{{ __('admin.print.price') }}</th><th class="text-end">{{ __('admin.print.total') }}</th></tr></thead>
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
    <p class="text-end"><strong>{{ __('admin.print.total') }}:</strong> {{ number_format($invoice->total, 2) }}</p>
    <p class="text-end"><strong>{{ __('admin.print.paid') }}:</strong> {{ number_format($invoice->paid_amount, 2) }}</p>
    <p class="text-end"><strong>{{ __('admin.print.remaining') }}:</strong> {{ number_format($invoice->remaining_amount, 2) }}</p>
    <p class="text-end"><strong>{{ __('admin.print.status') }}:</strong> {{ __('admin.invoices.payment_'.$invoice->payment_status) }}</p>
</div>
</body>
</html>
