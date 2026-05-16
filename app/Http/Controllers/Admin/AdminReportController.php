<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function dailySales(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        [$start, $end] = $this->dayBounds($date);

        $baseQuery = Invoice::query()->whereBetween('created_at', [$start, $end]);

        $totals = [
            'count' => (int) (clone $baseQuery)->count(),
            'sales' => (float) (clone $baseQuery)->sum('total'),
            'paid' => (float) (clone $baseQuery)->sum('paid_amount'),
            'remaining' => (float) (clone $baseQuery)->sum('remaining_amount'),
        ];

        $invoices = (clone $baseQuery)
            ->with('customer')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.reports.daily_sales', compact('date', 'invoices', 'totals'));
    }

    public function dailySalesCsv(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        [$start, $end] = $this->dayBounds($date);
        $filename = 'daily-sales-'.$date.'.csv';

        return response()->streamDownload(function () use ($start, $end) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Invoice', 'Customer', 'Total', 'Paid', 'Remaining', 'Status', 'Date']);

            Invoice::query()
                ->with('customer')
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('id')
                ->chunk(500, function ($rows) use ($out) {
                    foreach ($rows as $inv) {
                        fputcsv($out, [
                            $inv->invoice_number,
                            optional($inv->customer)->name,
                            $inv->total,
                            $inv->paid_amount,
                            $inv->remaining_amount,
                            $inv->payment_status,
                            $inv->created_at,
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function monthlySales(Request $request)
    {
        $month = (int) $request->get('month', Carbon::now()->month);
        $year = (int) $request->get('year', Carbon::now()->year);

        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end = (clone $start)->endOfMonth()->endOfDay();

        $rows = Invoice::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('substr(created_at, 1, 10) as d, COUNT(*) as c, SUM(total) as total_sum')
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $monthTotal = (float) Invoice::query()
            ->whereBetween('created_at', [$start, $end])
            ->sum('total');

        return view('admin.reports.monthly_sales', compact('month', 'year', 'rows', 'monthTotal'));
    }

    public function profit(Request $request)
    {
        $from = $request->get('from', Carbon::today()->startOfMonth()->toDateString());
        $to = $request->get('to', Carbon::today()->toDateString());
        [$start, $end] = $this->dateBounds($from, $to);

        $profit = $this->profitBetween($start, $end);
        $revenue = $this->revenueBetween($start, $end);

        return view('admin.reports.profit', compact('from', 'to', 'profit', 'revenue'));
    }

    public function inventory(Request $request)
    {
        $query = Product::query()->with('category')->orderBy('name');
        if ($request->boolean('low_only')) {
            $query->whereColumn('stock_quantity', '<=', 'minimum_stock_alert');
        }
        $products = $query->paginate(50)->withQueryString();

        return view('admin.reports.inventory', compact('products'));
    }

    public function inventoryCsv(Request $request)
    {
        $filename = 'inventory-'.date('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['SKU', 'Name', 'Category', 'Stock', 'Min alert', 'Retail', 'Wholesale', 'Purchase', 'Status']);

            Product::query()->with('category')->orderBy('name')->chunk(500, function ($products) use ($out) {
                foreach ($products as $p) {
                    fputcsv($out, [
                        $p->sku,
                        $p->name,
                        optional($p->category)->name,
                        $p->stock_quantity,
                        $p->minimum_stock_alert,
                        $p->retail_price,
                        $p->wholesale_price,
                        $p->purchase_price,
                        $p->status,
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function debts(Request $request)
    {
        $customers = Customer::query()
            ->orderByDesc('total_debt')
            ->paginate(30)
            ->withQueryString();

        return view('admin.reports.debts', compact('customers'));
    }

    public function debtsCsv()
    {
        $filename = 'customer-debts-'.date('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Customer', 'Phone', 'Total debt']);

            Customer::query()->orderByDesc('total_debt')->chunk(500, function ($customers) use ($out) {
                foreach ($customers as $c) {
                    fputcsv($out, [$c->name, $c->phone, $c->total_debt]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function topProducts(Request $request)
    {
        $from = $request->get('from', Carbon::today()->subDays(30)->toDateString());
        $to = $request->get('to', Carbon::today()->toDateString());
        $limit = min(50, max(5, (int) $request->get('limit', 10)));
        [$start, $end] = $this->dateBounds($from, $to);

        $rows = $this->topProductRows($start, $end, $limit);
        $products = Product::query()->whereIn('id', $rows->pluck('product_id'))->get()->keyBy('id');

        return view('admin.reports.top_products', compact('from', 'to', 'limit', 'rows', 'products'));
    }

    public function topProductsCsv(Request $request)
    {
        $from = $request->get('from', Carbon::today()->subDays(30)->toDateString());
        $to = $request->get('to', Carbon::today()->toDateString());
        $limit = min(50, max(5, (int) $request->get('limit', 10)));
        [$start, $end] = $this->dateBounds($from, $to);

        $rows = $this->topProductRows($start, $end, $limit);
        $products = Product::query()->whereIn('id', $rows->pluck('product_id'))->get()->keyBy('id');
        $filename = 'top-products-'.$from.'-to-'.$to.'.csv';

        return response()->streamDownload(function () use ($rows, $products) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['SKU', 'Name', 'Qty sold', 'Revenue']);
            foreach ($rows as $r) {
                $p = $products->get($r->product_id);
                fputcsv($out, [
                    $p ? $p->sku : $r->product_id,
                    $p ? $p->name : '',
                    $r->qty_sum,
                    $r->revenue_sum,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function profitPdf(Request $request)
    {
        $from = $request->get('from', Carbon::today()->startOfMonth()->toDateString());
        $to = $request->get('to', Carbon::today()->toDateString());
        [$start, $end] = $this->dateBounds($from, $to);

        $profit = $this->profitBetween($start, $end);
        $revenue = $this->revenueBetween($start, $end);

        $pdf = Pdf::loadView('admin.reports.profit_pdf', compact('from', 'to', 'profit', 'revenue'));

        return $pdf->download('profit-'.$from.'-'.$to.'.pdf');
    }

    /**
     * @return array{0: \Carbon\Carbon, 1: \Carbon\Carbon}
     */
    protected function dayBounds(string $date): array
    {
        $day = Carbon::parse($date);

        return [$day->copy()->startOfDay(), $day->copy()->endOfDay()];
    }

    /**
     * @return array{0: \Carbon\Carbon, 1: \Carbon\Carbon}
     */
    protected function dateBounds(string $from, string $to): array
    {
        return [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay(),
        ];
    }

    protected function profitBetween(Carbon $start, Carbon $end): float
    {
        return (float) InvoiceItem::query()
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->whereBetween('invoices.created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(line_total - (unit_cost * quantity)), 0) as p')
            ->value('p');
    }

    protected function revenueBetween(Carbon $start, Carbon $end): float
    {
        return (float) Invoice::query()
            ->whereBetween('created_at', [$start, $end])
            ->sum('total');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function topProductRows(Carbon $start, Carbon $end, int $limit)
    {
        return InvoiceItem::query()
            ->select('invoice_items.product_id', DB::raw('SUM(invoice_items.quantity) as qty_sum'), DB::raw('SUM(invoice_items.line_total) as revenue_sum'))
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->whereBetween('invoices.created_at', [$start, $end])
            ->groupBy('invoice_items.product_id')
            ->orderByDesc('qty_sum')
            ->limit($limit)
            ->get();
    }
}
