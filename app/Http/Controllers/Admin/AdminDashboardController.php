<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $cacheKey = 'admin.dashboard.'.$today->toDateString();

        $stats = Cache::remember($cacheKey, 120, function () use ($today) {
            $start = $today->copy()->startOfDay();
            $end = $today->copy()->endOfDay();

            $totalSalesToday = (float) Invoice::query()
                ->whereBetween('created_at', [$start, $end])
                ->sum('total');

            $profitToday = (float) InvoiceItem::query()
                ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                ->whereBetween('invoices.created_at', [$start, $end])
                ->selectRaw('COALESCE(SUM(line_total - (unit_cost * quantity)), 0) as p')
                ->value('p');

            $pendingDebts = (float) Customer::query()->sum('total_debt');
            $totalProducts = Product::query()->count();
            $lowStockCount = Product::query()
                ->whereColumn('stock_quantity', '<=', 'minimum_stock_alert')
                ->count();

            $lowStockProducts = Product::query()
                ->with('category')
                ->whereColumn('stock_quantity', '<=', 'minimum_stock_alert')
                ->orderBy('stock_quantity')
                ->limit(10)
                ->get();

            $latestInvoices = Invoice::query()
                ->with('customer')
                ->latest('id')
                ->limit(8)
                ->get();

            $latestPayments = Payment::query()
                ->with(['customer', 'invoice'])
                ->latest('id')
                ->limit(8)
                ->get();

            return compact(
                'totalSalesToday',
                'profitToday',
                'pendingDebts',
                'totalProducts',
                'lowStockCount',
                'lowStockProducts',
                'latestInvoices',
                'latestPayments'
            );
        });

        return view('admin.dashboard', $stats);
    }
}
