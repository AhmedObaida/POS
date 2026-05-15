<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = Customer::query()->orderBy('name');
        if ($request->filled('search')) {
            $s = $request->get('search');
            $q->where(function ($query) use ($s) {
                $query->where('name', 'like', '%'.$s.'%')
                    ->orWhere('phone', 'like', '%'.$s.'%');
            });
        }
        $customers = $q->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        Customer::query()->create($request->validated());

        return redirect()->route('admin.customers.index')->with('success', 'Customer created.');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'invoices' => function ($q) {
                $q->latest()->limit(50);
            },
            'payments' => function ($q) {
                $q->latest()->limit(50);
            },
        ]);

        $remainingFromInvoices = (float) $customer->invoices()->sum('remaining_amount');

        return view('admin.customers.show', compact('customer', 'remainingFromInvoices'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->invoices()->exists()) {
            return redirect()->route('admin.customers.index')->with('error', 'Cannot delete a customer with invoices.');
        }
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted.');
    }
}
