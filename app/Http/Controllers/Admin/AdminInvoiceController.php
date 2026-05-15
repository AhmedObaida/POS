<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use InvalidArgumentException;

class AdminInvoiceController extends Controller
{
    /** @var InvoiceService */
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request)
    {
        $query = Invoice::query()->with(['customer', 'creator'])->latest();

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where('invoice_number', 'like', '%'.$s.'%');
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->get('payment_status'));
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        $invoices = $query->paginate(20)->withQueryString();
        $customers = Customer::query()->orderBy('name')->get();

        return view('admin.invoices.index', compact('invoices', 'customers'));
    }

    public function create()
    {
        $customers = Customer::query()->orderBy('name')->get();
        $productOptions = Product::query()->active()->orderBy('name')->get([
            'id', 'name', 'sku', 'stock_quantity', 'retail_price', 'wholesale_price',
        ]);

        return view('admin.invoices.create', compact('customers', 'productOptions'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        try {
            $invoice = $this->invoiceService->createInvoice($request->validated(), $request->user());
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['items.product', 'customer', 'creator', 'payments']);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function printInvoice(Invoice $invoice)
    {
        $invoice->load(['items.product', 'customer', 'creator']);

        return view('admin.invoices.print', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['items.product', 'customer', 'creator']);
        $pdf = Pdf::loadView('admin.invoices.print', compact('invoice'));

        return $pdf->download($invoice->invoice_number.'.pdf');
    }
}
