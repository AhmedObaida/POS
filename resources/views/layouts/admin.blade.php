@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" data-bs-theme="light" id="adminHtmlRoot">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('admin.brand')) — {{ config('app.name', 'POS') }}</title>
    @if ($isRtl)
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-rtl.min.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @endif
</head>
<body class="bg-light">
<div class="d-flex flex-column flex-md-row" style="min-height:100vh">
    <aside class="bg-white {{ $isRtl ? 'border-start' : 'border-end' }} flex-shrink-0" style="width: 260px;">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <a href="{{ route('admin.dashboard') }}" class="fw-bold text-decoration-none text-dark">{{ __('admin.brand') }}</a>
            <button type="button" class="btn btn-sm btn-outline-secondary d-md-none" data-bs-toggle="collapse" data-bs-target="#sidebarNav" aria-expanded="false">{{ __('admin.menu') }}</button>
        </div>
        <div id="sidebarNav" class="collapse d-md-block">
            <nav class="nav flex-column p-2 small">
                <a class="nav-link rounded {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.dashboard') }}">{{ __('admin.nav.dashboard') }}</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.categories.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.categories.index') }}">{{ __('admin.nav.categories') }}</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.products.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.products.index') }}">{{ __('admin.nav.products') }}</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.customers.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.customers.index') }}">{{ __('admin.nav.customers') }}</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.invoices.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.invoices.index') }}">{{ __('admin.nav.invoices') }}</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.payments.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.payments.index') }}">{{ __('admin.nav.payments') }}</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.inventory.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.inventory.index') }}">{{ __('admin.nav.inventory') }}</a>
                <hr class="my-2">
                <span class="text-muted text-uppercase px-2" style="font-size: 0.7rem;">{{ __('admin.nav.reports') }}</span>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.daily-sales*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.daily-sales') }}">{{ __('admin.nav.daily_sales') }}</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.monthly-sales*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.monthly-sales') }}">{{ __('admin.nav.monthly_sales') }}</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.profit*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.profit') }}">{{ __('admin.nav.profit') }}</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.inventory*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.inventory') }}">{{ __('admin.nav.inventory_report') }}</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.debts*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.debts') }}">{{ __('admin.nav.customer_debts') }}</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.top-products*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.top-products') }}">{{ __('admin.nav.top_products') }}</a>
                <hr class="my-2">
                <div class="px-2 mb-2 d-flex gap-1 flex-wrap">
                    <form method="post" action="{{ route('locale.switch') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="locale" value="en">
                        <button type="submit" class="btn btn-sm {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-outline-secondary' }}">{{ __('admin.lang_en') }}</button>
                    </form>
                    <form method="post" action="{{ route('locale.switch') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="locale" value="ar">
                        <button type="submit" class="btn btn-sm {{ app()->getLocale() === 'ar' ? 'btn-primary' : 'btn-outline-secondary' }}">{{ __('admin.lang_ar') }}</button>
                    </form>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="px-2">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm w-100" type="submit">{{ __('admin.logout') }} ({{ Auth::user()->name }})</button>
                </form>
                <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-2" id="themeToggle">{{ __('admin.theme_toggle') }}</button>
            </nav>
        </div>
    </aside>
    <main class="flex-grow-1 p-3 p-md-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('admin.close') }}"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('admin.close') }}"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/admin/entity-picker.js') }}"></script>
<script>
(function () {
    var root = document.getElementById('adminHtmlRoot');
    var key = 'pos_admin_theme';
    var saved = localStorage.getItem(key);
    if (saved === 'dark') { root.setAttribute('data-bs-theme', 'dark'); }
    var btn = document.getElementById('themeToggle');
    if (btn) {
        btn.addEventListener('click', function () {
            var cur = root.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
            var next = cur === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-bs-theme', next);
            localStorage.setItem(key, next);
        });
    }
})();
</script>
@stack('scripts')
</body>
</html>
