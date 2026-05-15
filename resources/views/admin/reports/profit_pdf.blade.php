<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; }
    </style>
</head>
<body>
    <h1>Profit summary</h1>
    <p>From {{ $from }} to {{ $to }}</p>
    <p><strong>Revenue:</strong> {{ number_format($revenue, 2) }}</p>
    <p><strong>Gross profit:</strong> {{ number_format($profit, 2) }}</p>
</body>
</html>
