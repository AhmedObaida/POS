@php $isRtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; }
    </style>
</head>
<body>
    <h1>{{ __('admin.reports.profit_pdf_title') }}</h1>
    <p>{{ __('admin.reports.profit_pdf_from') }} {{ $from }} {{ __('admin.reports.profit_pdf_to') }} {{ $to }}</p>
    <p><strong>{{ __('admin.reports.profit_pdf_revenue') }}:</strong> {{ number_format($revenue, 2) }}</p>
    <p><strong>{{ __('admin.reports.profit_pdf_gross') }}:</strong> {{ number_format($profit, 2) }}</p>
</body>
</html>
