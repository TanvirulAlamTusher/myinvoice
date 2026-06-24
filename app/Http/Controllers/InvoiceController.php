<?php
namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('customer_business_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->latest('invoice_date')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('invoices.partials.table', compact('invoices'))->render(),
            ]);
        }

        $totalInvoices  = Invoice::count();
        $completedTotal = Invoice::where('status', 'completed')->sum('grand_total');
        $paidTotal      = Invoice::where('status', 'completed')->sum('paid_amount');
        $dueTotal       = max(0, $completedTotal - $paidTotal);

        return view('invoices.index', compact(
            'invoices',
            'totalInvoices',
            'completedTotal',
            'paidTotal',
            'dueTotal'
        ));
    }

    public function create()
    {
        return view('invoices.create', [
            'invoice'   => new Invoice([
                'invoice_no'     => $this->nextInvoiceNo(),
                'invoice_date'   => now(),
                'payment_method' => 'cash',
                'status'         => 'completed',
            ]),

            'customers' => Customer::withCount('invoices')
                ->where('is_active', true)
                ->orderByDesc('invoices_count')
                ->orderBy('name')
                ->get(),

            'products'  => Product::with('productUnit')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        try {
            DB::transaction(function () use ($data) {
                $invoice = Invoice::create($this->invoicePayload($data));
                $this->syncItems($invoice, $data['items']);
                $this->applyStock($invoice);
            });
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully');
    }

    public function show($id)
    {
        $invoice = Invoice::withTrashed()
            ->with([
                'items.product.brand',
                'items.returnItems',
                'productReturns.items',
                'productReturns.creator',
                'customer',
                'creator',
                'updater',
            ])
            ->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }
    public function edit(Invoice $invoice)
    {
        $invoice->load('items');

        return view('invoices.edit', [
            'invoice'   => $invoice,
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'products'  => Product::with('productUnit')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $this->validatedData($request, $invoice);

        try {
            DB::transaction(function () use ($invoice, $data) {
                $invoice->load('items');
                $this->reverseStock($invoice);
                $invoice->update($this->invoicePayload($data, true));
                $invoice->items()->delete();
                $this->syncItems($invoice, $data['items']);
                $this->applyStock($invoice->fresh('items'));
            });
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully');
    }

    public function destroy(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            $invoice->load('items');
            $this->reverseStock($invoice);
            $invoice->delete();
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice moved to Recycle bin');
    }

    private function validatedData(Request $request, ?Invoice $invoice = null): array
    {
        $invoiceNoRule = $invoice
            ? 'required|string|max:255|unique:invoices,invoice_no,' . $invoice->id
            : 'required|string|max:255|unique:invoices,invoice_no';

        return $request->validate([
            'invoice_no'             => $invoiceNoRule,
            'invoice_date'           => 'required|date',
            'customer_id'            => 'nullable|exists:customers,id',
            'customer_name'          => 'nullable|string|max:255',
            'customer_business_name' => 'nullable|string|max:255',
            'customer_phone'         => 'nullable|string|max:30',
            'customer_email'         => 'nullable|email|max:255',
            'customer_address'       => 'nullable|string',
            'discount_amount'        => 'nullable|numeric|min:0',
            'tax_amount'             => 'nullable|numeric|min:0',
            'paid_amount'            => 'nullable|numeric|min:0',
            'payment_method'         => 'required|in:cash,bank,cheque,mobile_banking,credit',
            'status'                 => 'required|in:draft,completed,cancelled',
            'note'                   => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'nullable|exists:products,id',
            'items.*.product_name'   => 'required|string|max:255',
            'items.*.product_sku'    => 'nullable|string|max:255',
            'items.*.quantity'       => 'required|numeric|min:0.01',
            'items.*.unit'           => 'nullable|string|max:255',
            'items.*.purchase_price' => 'nullable|numeric|min:0',
            'items.*.unit_price'     => 'required|numeric|min:0',
        ]);
    }

    private function invoicePayload(array $data, bool $updating = false): array
    {
        $customer   = ! empty($data['customer_id']) ? Customer::find($data['customer_id']) : null;
        $subTotal   = collect($data['items'])->sum(fn($item) => (float) $item['quantity'] * (float) $item['unit_price']);
        $discount   = (float) ($data['discount_amount'] ?? 0);
        $tax        = (float) ($data['tax_amount'] ?? 0);
        $grandTotal = max(0, $subTotal - $discount + $tax);
        $paidAmount = min((float) ($data['paid_amount'] ?? 0), $grandTotal);

        $payload = [
            'invoice_no'             => $data['invoice_no'],
            'invoice_date'           => $data['invoice_date'],
            'customer_id'            => $customer?->id,
            'customer_name'          => $customer?->name ?? $data['customer_name'] ?? null,
            'customer_business_name' => $customer?->business_name ?? $data['customer_business_name'] ?? null,
            'customer_phone'         => $customer?->phone ?? $data['customer_phone'] ?? null,
            'customer_email'         => $customer?->email ?? $data['customer_email'] ?? null,
            'customer_address'       => $customer?->address ?? $data['customer_address'] ?? null,
            'sub_total'              => $subTotal,
            'discount_amount'        => $discount,
            'tax_amount'             => $tax,
            'grand_total'            => $grandTotal,
            'paid_amount'            => $paidAmount,
            'payment_status'         => $this->paymentStatus($grandTotal, $paidAmount),
            'payment_method'         => $data['payment_method'],
            'status'                 => $data['status'],
            'note'                   => $data['note'] ?? null,
        ];

        $payload[$updating ? 'updated_by' : 'created_by'] = auth()->id();

        return $payload;
    }

    private function syncItems(Invoice $invoice, array $items): void
    {
        foreach ($items as $item) {
            $product = ! empty($item['product_id']) ? Product::with('productUnit')->find($item['product_id']) : null;

            $invoice->items()->create([
                'product_id'     => $product?->id,
                'product_name'   => $product?->name ?? $item['product_name'],
                'product_sku'    => $product?->sku ?? $item['product_sku'] ?? null,
                'quantity'       => $item['quantity'],
                'unit'           => $product?->productUnit?->name ?? $item['unit'] ?? null,
                'purchase_price' => $item['purchase_price'] ?? $product?->purchase_price ?? 0,
                'unit_price'     => $item['unit_price'],
                'subtotal'       => (float) $item['quantity'] * (float) $item['unit_price'],
            ]);
        }
    }

    private function applyStock(Invoice $invoice): void
    {
        if ($invoice->status !== 'completed') {
            return;
        }

        foreach ($invoice->items as $item) {
            if ($item->product_id) {
                Product::whereKey($item->product_id)->decrement('stock', (float) $item->quantity);
            }
        }
    }

    private function reverseStock(Invoice $invoice): void
    {
        if ($invoice->status !== 'completed') {
            return;
        }

        foreach ($invoice->items as $item) {
            if ($item->product_id) {
                Product::whereKey($item->product_id)->increment('stock', (float) $item->quantity);
            }
        }
    }

    private function paymentStatus(float $grandTotal, float $paidAmount): string
    {
        if ($grandTotal <= 0 || $paidAmount >= $grandTotal) {
            return 'paid';
        }

        return $paidAmount > 0 ? 'partial' : 'due';
    }

    private function nextInvoiceNo(): string
    {
        $prefix = 'AE-' . now()->format('Ymd') . '-';

        $latestNumber = Invoice::withTrashed()
            ->where('invoice_no', 'like', $prefix . '%')
            ->selectRaw("MAX(CAST(SUBSTRING(invoice_no, -4) AS UNSIGNED)) as max_no")
            ->value('max_no');

        $next = $latestNumber ? $latestNumber + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    // trash, restore, force delete methods can be added here for soft deleted invoices
    public function trash(Request $request)
    {
        $query = Invoice::onlyTrashed()
            ->with(['customer', 'creator', 'items']);

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('customer_business_name', 'like', "%{$search}%");

            });
        }

        $invoices = $query
            ->latest('deleted_at')
            ->paginate(20)
            ->withQueryString();

        return view('invoices.trash', compact('invoices'));
    }
    public function restore($id)
    {
        $invoice = Invoice::onlyTrashed()->with('items')->findOrFail($id);

        DB::transaction(function () use ($invoice) {

            // restore invoice
            $invoice->restore();

            // deduct stock again
            foreach ($invoice->items as $item) {

                if ($item->product_id) {

                    Product::whereKey($item->product_id)
                        ->decrement('stock', (float) $item->quantity);
                }
            }
        });

        return redirect()
            ->route('invoices.trash')
            ->with('success', 'Invoice restored successfully');
    }
    public function forceDelete($id)
    {
        $invoice = Invoice::onlyTrashed()->findOrFail($id);

        $invoice->forceDelete();

        return redirect()
            ->route('invoices.trash')
            ->with('success', 'Invoice permanently deleted');
    }

    /*
    |--------------------------------------------------------------------------
    | SELECTED TRASH FORCE DELETE
    |--------------------------------------------------------------------------
    */

    public function forceDeleteSelected(Request $request)
    {
        $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $invoices = Invoice::onlyTrashed()
            ->whereIn('id', $request->ids)
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->forceDelete();
        }

        return redirect()
            ->route('invoices.trash')
            ->with('success', count($invoices) . ' invoices permanently deleted');
    }
    /*
    |--------------------------------------------------------------------------
    | ALL TRASH FORCE DELETE
    |--------------------------------------------------------------------------
    */

    public function forceDeleteAll()
    {
        $invoices = Invoice::onlyTrashed()->get();

        foreach ($invoices as $invoice) {
            $invoice->forceDelete();
        }

        return redirect()
            ->route('invoices.trash')
            ->with('success', 'All trashed invoices permanently deleted');
    }

// print invoice
    public function printInvoice(Invoice $invoice)
    {
        $business = BusinessSetting::first();

        if (request()->ajax()) {
            return view('invoices.templates.classic', compact('invoice', 'business'))->render();
        }

        return view('invoices.templates.classic', compact('invoice', 'business'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        $business = BusinessSetting::first();

        $html = view(
            'invoices.templates.classic_pdf',
            compact('invoice', 'business')
        )->render();

        $mpdf = new \Mpdf\Mpdf([
            'format'        => 'A4',
            'margin_top'    => 5,
            'margin_bottom' => 5,
            'margin_left'   => 5,
            'margin_right'  => 5,
        ]);

        // বাংলা ফন্ট
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont   = true;

        $mpdf->WriteHTML($html);

        return response(
            $mpdf->Output('', 'S'),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' =>
                'attachment; filename="'. $invoice->invoice_no . '.pdf"',
            ]
        );
    }
public function sharePdfFile(Invoice $invoice)
{
    $business = BusinessSetting::first();

    $html = view(
        'invoices.templates.classic_pdf',
        compact('invoice', 'business')
    )->render();

    $mpdf = new \Mpdf\Mpdf([
        'format'        => 'A4',
        'margin_top'    => 5,
        'margin_bottom' => 5,
        'margin_left'   => 5,
        'margin_right'  => 5,
    ]);

    $mpdf->autoScriptToLang = true;
    $mpdf->autoLangToFont   = true;

    $mpdf->WriteHTML($html);

    return response(
        $mpdf->Output('', 'S'),
        200,
        [
            'Content-Type' => 'application/pdf',


        ]
    );
}

}
