<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class AdminPaymentController extends Controller
{
    /** @var PaymentService */
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $query = Payment::query()->with(['customer', 'invoice', 'creator'])->latest();

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }
        if ($request->filled('from')) {
            $query->whereDate('payment_date', '>=', $request->get('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('payment_date', '<=', $request->get('to'));
        }

        $payments = $query->paginate(20)->withQueryString();
        $customers = Customer::query()->orderBy('name')->get();

        return view('admin.payments.index', compact('payments', 'customers'));
    }

    public function create(Request $request)
    {
        $customers = Customer::query()->orderBy('name')->get();
        $selectedCustomerId = $request->get('customer_id');
        $selectedInvoiceId = $request->get('invoice_id');
        $invoices = collect();
        if ($selectedCustomerId) {
            $invoices = Invoice::query()
                ->where('customer_id', $selectedCustomerId)
                ->where('remaining_amount', '>', 0)
                ->orderByDesc('id')
                ->get();
        }

        return view('admin.payments.create', compact('customers', 'invoices', 'selectedCustomerId', 'selectedInvoiceId'));
    }

    public function store(StorePaymentRequest $request)
    {
        try {
            $this->paymentService->recordPayment($request->validated(), $request->user());
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->withErrors(['amount' => $e->getMessage()]);
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment recorded.');
    }

    public function openInvoices(Request $request)
    {
        $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
        ]);
        $invoices = Invoice::query()
            ->where('customer_id', $request->get('customer_id'))
            ->where('remaining_amount', '>', 0)
            ->orderByDesc('id')
            ->get(['id', 'invoice_number', 'total', 'paid_amount', 'remaining_amount', 'created_at']);

        return response()->json($invoices);
    }
}
