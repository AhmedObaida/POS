<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceRequest;
use App\Models\Customer;
use App\Models\Invoice;
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
        $query = Invoice::query()->with(['customer', 'creator'])->latest('id');

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
        $filterCustomer = $this->resolveCustomerForPicker($request->get('customer_id'));

        return view('admin.invoices.index', compact('invoices', 'filterCustomer'));
    }

    public function create(Request $request)
    {
        $selectedCustomerId = old('customer_id', $request->get('customer_id'));
        $selectedCustomer = $this->resolveCustomerForPicker($selectedCustomerId);

        return view('admin.invoices.create', compact('selectedCustomer'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        try {
            $invoice = $this->invoiceService->createInvoice($request->validated(), $request->user());
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()->route('admin.invoices.show', $invoice)->with('success', __('messages.invoice_created'));
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

    /**
     * @param  int|string|null  $customerId
     * @return object{id: int, name: string, phone: ?string}|null
     */
    protected function resolveCustomerForPicker($customerId)
    {
        if (! $customerId) {
            return null;
        }

        $customer = Customer::query()->find($customerId);
        if (! $customer) {
            return null;
        }

        return (object) [
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone,
        ];
    }
}
