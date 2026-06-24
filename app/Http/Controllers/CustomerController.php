<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('alternative_phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $customers = $query
            ->orderBy('is_active', 'desc')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('customers.partials.table', compact('customers'))->render(),
            ]);
        }

        $totalCustomers = Customer::count();
        $totalActiveCustomers = Customer::where('is_active', true)->count();
        $totalInactiveCustomers = Customer::where('is_active', false)->count();

        return view('customers.index', compact(
            'customers',
            'totalCustomers',
            'totalActiveCustomers',
            'totalInactiveCustomers'
        ));
    }

    /**
     * Store customer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'business_name'     => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:30',
            'alternative_phone' => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:255',
            'address'           => 'nullable|string',
            'is_active'         => 'nullable|boolean',
        ]);

        Customer::create([
            'name'              => $request->name,
            'business_name'     => $request->business_name,
            'phone'             => $request->phone,
            'alternative_phone' => $request->alternative_phone,
            'email'             => $request->email,
            'address'           => $request->address,
            'is_active'         => $request->has('is_active'),
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully');
    }

    /**
     * Show single customer.
     */
public function show(Request $request, Customer $customer)
{
    $search = $request->search;

    $invoices = $customer->invoices()
        ->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('invoice_no', 'like', "%{$search}%")
                    ->orWhere('payment_status', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->paginate(20)
        ->withQueryString();

    // AJAX RESPONSE (search OR pagination)
    if ($request->ajax()) {
        return view('customers.partials.invoice-table', compact('invoices', 'customer'))->render();
    }

    return view('customers.show', compact('customer', 'invoices'));
}
    /**
     * Update customer.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'business_name'     => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:30',
            'alternative_phone' => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:255',
            'address'           => 'nullable|string',
            'is_active'         => 'nullable|boolean',
        ]);

        $customer->update([
            'name'              => $request->name,
            'business_name'     => $request->business_name,
            'phone'             => $request->phone,
            'alternative_phone' => $request->alternative_phone,
            'email'             => $request->email,
            'address'           => $request->address,
            'is_active'         => $request->has('is_active'),
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully');
    }

    /**
     * Delete customer.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully');
    }
}
