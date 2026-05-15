<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light" id="adminHtmlRoot">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'POS') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
<div class="d-flex flex-column flex-md-row" style="min-height:100vh">
    <aside class="bg-white border-end flex-shrink-0" style="width: 260px;">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <a href="{{ route('admin.dashboard') }}" class="fw-bold text-decoration-none text-dark">POS Admin</a>
            <button type="button" class="btn btn-sm btn-outline-secondary d-md-none" data-bs-toggle="collapse" data-bs-target="#sidebarNav" aria-expanded="false">Menu</button>
        </div>
        <div id="sidebarNav" class="collapse d-md-block">
            <nav class="nav flex-column p-2 small">
                <a class="nav-link rounded {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.categories.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.categories.index') }}">Categories</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.products.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.products.index') }}">Products</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.customers.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.customers.index') }}">Customers</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.invoices.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.invoices.index') }}">Invoices</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.payments.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.payments.index') }}">Payments</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.inventory.*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.inventory.index') }}">Inventory</a>
                <hr class="my-2">
                <span class="text-muted text-uppercase px-2" style="font-size: 0.7rem;">Reports</span>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.daily-sales*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.daily-sales') }}">Daily sales</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.monthly-sales*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.monthly-sales') }}">Monthly sales</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.profit*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.profit') }}">Profit</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.inventory*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.inventory') }}">Inventory report</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.debts*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.debts') }}">Customer debts</a>
                <a class="nav-link rounded {{ request()->routeIs('admin.reports.top-products*') ? 'active bg-primary text-white' : '' }}" href="{{ route('admin.reports.top-products') }}">Top products</a>
                <hr class="my-2">
                <form method="POST" action="{{ route('logout') }}" class="px-2">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm w-100" type="submit">Logout ({{ Auth::user()->name }})</button>
                </form>
                <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-2" id="themeToggle">Toggle dark mode</button>
            </nav>
        </div>
    </aside>
    <main class="flex-grow-1 p-3 p-md-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
