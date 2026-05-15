<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalSalesToday = (float) Invoice::query()->whereDate('created_at', $today)->sum('total');

        $profitToday = (float) (InvoiceItem::query()
            ->whereHas('invoice', function ($q) use ($today) {
                $q->whereDate('created_at', $today);
            })
            ->selectRaw('COALESCE(SUM(line_total - (unit_cost * quantity)), 0) as p')
            ->value('p'));

        $pendingDebts = (float) Customer::query()->sum('total_debt');
        $totalProducts = Product::query()->count();
        $lowStockCount = Product::query()->whereColumn('stock_quantity', '<=', 'minimum_stock_alert')->count();
        $lowStockProducts = Product::query()
            ->with('category')
            ->whereColumn('stock_quantity', '<=', 'minimum_stock_alert')
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        $latestInvoices = Invoice::query()
            ->with('customer')
            ->latest()
            ->limit(8)
            ->get();

        $latestPayments = Payment::query()
            ->with(['customer', 'invoice'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalSalesToday',
            'profitToday',
            'pendingDebts',
            'totalProducts',
            'lowStockCount',
            'lowStockProducts',
            'latestInvoices',
            'latestPayments'
        ));
    }
}
