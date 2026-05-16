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
        $query = Payment::query()->with(['customer', 'invoice', 'creator'])->latest('id');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }
        if ($request->filled('from')) {
            $query->where('payment_date', '>=', $request->get('from'));
        }
        if ($request->filled('to')) {
            $query->where('payment_date', '<=', $request->get('to'));
        }

        $payments = $query->paginate(20)->withQueryString();
        $filterCustomer = $this->resolveCustomerForPicker($request->get('customer_id'));

        return view('admin.payments.index', compact('payments', 'filterCustomer'));
    }

    public function create(Request $request)
    {
        $selectedCustomerId = old('customer_id', $request->get('customer_id'));
        $selectedInvoiceId = old('invoice_id', $request->get('invoice_id'));
        $selectedCustomer = $this->resolveCustomerForPicker($selectedCustomerId);
        $invoices = collect();
        if ($selectedCustomerId) {
            $invoices = Invoice::query()
                ->where('customer_id', $selectedCustomerId)
                ->where('remaining_amount', '>', 0)
                ->orderByDesc('id')
                ->get();
        }

        return view('admin.payments.create', compact('invoices', 'selectedCustomer', 'selectedCustomerId', 'selectedInvoiceId'));
    }

    public function store(StorePaymentRequest $request)
    {
        try {
            $this->paymentService->recordPayment($request->validated(), $request->user());
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->withErrors(['amount' => $e->getMessage()]);
        }

        return redirect()->route('admin.payments.index')->with('success', __('messages.payment_recorded'));
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
