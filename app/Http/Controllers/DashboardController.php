<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductReturn;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $completedInvoices = Invoice::where('status', 'completed');

        $totalSales = (clone $completedInvoices)->sum('grand_total');
        $paidAmount = (clone $completedInvoices)->sum('paid_amount');
        $dueAmount = max(0, $totalSales - $paidAmount);
        $todaySales = (clone $completedInvoices)->whereDate('invoice_date', today())->sum('grand_total');
        $monthSales = (clone $completedInvoices)->whereBetween('invoice_date', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->sum('grand_total');

        $invoiceCount = Invoice::count();
        $customerCount = Customer::count();
        $productCount = Product::count();
        $lowStockCount = Product::whereColumn('stock', '<=', 'alert_stock')->count();
        $stockValue = Product::selectRaw('SUM(stock * purchase_price) as total')->value('total') ?? 0;
        $returnAmount = ProductReturn::sum('total_amount');

        $startDate = now()->subDays(6)->startOfDay();
        $salesByDate = Invoice::where('status', 'completed')
            ->where('invoice_date', '>=', $startDate)
            ->get(['invoice_date', 'grand_total'])
            ->groupBy(fn ($invoice) => $invoice->invoice_date->format('Y-m-d'))
            ->map(fn ($items) => $items->sum('grand_total'));

        $salesTrend = collect(CarbonPeriod::create($startDate, today()))
            ->map(function ($date) use ($salesByDate) {
                $key = $date->format('Y-m-d');

                return [
                    'label' => $date->format('M d'),
                    'total' => (float) ($salesByDate[$key] ?? 0),
                ];
            })
            ->values();

        $salesTrendMax = max(1, $salesTrend->max('total'));

        $topProducts = InvoiceItem::query()
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.status', 'completed')
            ->whereNull('invoices.deleted_at')
            ->selectRaw("COALESCE(invoice_items.product_name, 'Unknown Product') as product_name")
            ->selectRaw('SUM(invoice_items.quantity) as quantity')
            ->selectRaw('SUM(invoice_items.subtotal) as total')
            ->groupBy('product_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topProductsMax = max(1, (float) $topProducts->max('total'));

        $paymentStatus = Invoice::selectRaw('payment_status, COUNT(*) as count, SUM(grand_total) as total')
            ->where('status', 'completed')
            ->groupBy('payment_status')
            ->get()
            ->keyBy('payment_status');

        $recentInvoices = Invoice::with('customer')
            ->latest('invoice_date')
            ->latest()
            ->limit(10)
            ->get();

        $lowStockProducts = Product::with(['category', 'productUnit'])
            ->whereColumn('stock', '<=', 'alert_stock')
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalSales',
            'paidAmount',
            'dueAmount',
            'todaySales',
            'monthSales',
            'invoiceCount',
            'customerCount',
            'productCount',
            'lowStockCount',
            'stockValue',
            'returnAmount',
            'salesTrend',
            'salesTrendMax',
            'topProducts',
            'topProductsMax',
            'paymentStatus',
            'recentInvoices',
            'lowStockProducts'
        ));
    }
}
