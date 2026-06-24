<?php
namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // ================= INDEX =================
 public function index(Request $request)
{
    $query = Product::with(['category','brand','productUnit']);

    // 🔎 SEARCH
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name','like',"%$search%")
              ->orWhere('sku','like',"%$search%");
        });
    }

    // 🏷️ BRAND
    if ($request->filled('brand')) {
        if ($request->brand == 'none') {
            $query->whereNull('brand_id');
        } else {
            $query->where('brand_id',$request->brand);
        }
    }

    // 📂 CATEGORY
    if ($request->filled('category')) {
        $query->where('category_id',$request->category);
    }

    // ⚠️ STOCK FILTER
    if ($request->filled('stock')) {
        if ($request->stock == 'low') {
            $query->whereColumn('stock','<=','alert_stock');
        }
    }

    // 🔘 STATUS FILTER
    if ($request->filled('status')) {
        $query->where('is_active', $request->status == 'active');
    }

    $products = $query->latest()->paginate(20);

    $categories = Category::where('is_active',1)->get();
    $brands = Brand::where('is_active',1)->get();

    // 📊 TOTALS (GLOBAL - NOT FILTERED)
    $totalStock = Product::sum('stock');

    $totalPurchaseValue = Product::selectRaw('SUM(stock * purchase_price) as total')
        ->value('total') ?? 0;

    $totalSellingValue = Product::selectRaw('SUM(stock * sale_price) as total')
        ->value('total') ?? 0;

    // ================= AJAX =================
    if ($request->ajax()) {

        $html = view('products.partials.table', compact('products'))->render();

        return response()->json([
            'html' => $html
        ]);
    }

    return view('products.index', compact(
        'products',
        'categories',
        'brands',
        'totalStock',
        'totalPurchaseValue',
        'totalSellingValue'
    ));
}

    // ================= CREATE =================
    public function create()
    {
        $categories = Category::where('is_active', 1)->get();
        $brands     = Brand::where('is_active', 1)->get();
        $units      = ProductUnit::where('is_active', 1)->get();

        return view('products.create', compact(
            'categories',
            'brands',
            'units'
        ));
    }

    // ================= CREATE =================

    public function show($id)
    {
        $product = Product::with([
            'category',
            'brand',
            'productUnit',
        ])->findOrFail($id);

        return view('products.show', compact('product'));
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        try {
            $request->validate([

                'name'            => 'required|max:255',

                'sku'             => 'nullable|unique:products,sku',

                'barcode'         => 'nullable|unique:products,barcode',

                'category_id'     => 'nullable|exists:categories,id',

                'brand_id'        => 'nullable|exists:brands,id',

                'product_unit_id' => 'nullable|exists:product_units,id',

                'purchase_price'  => 'nullable|numeric|min:0',

                'sale_price'      => 'required|numeric|min:0',

                'stock'           => 'required|numeric|min:0',

                'alert_stock'     => 'required|numeric|min:0',

                'weight'          => 'nullable|numeric|min:0',

                'description'     => 'nullable',

                'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            ]);

        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Validation failed: ' . $e->getMessage());
        }

        try {

            $imagePath = null;

            if ($request->hasFile('image')) {

                $imagePath = $request->file('image')
                    ->store('productsImages', 'public');
            }

            Product::create([

                'name'            => $request->name,

                'sku'             => $request->sku,

                'barcode'         => $request->barcode,

                'category_id'     => $request->category_id,

                'brand_id'        => $request->brand_id,

                'product_unit_id' => $request->product_unit_id,

                'purchase_price'  => $request->purchase_price,

                'sale_price'      => $request->sale_price,

                'stock'           => $request->stock,

                'alert_stock'     => $request->alert_stock,

                'weight'          => $request->weight,

                'description'     => $request->description,

                'image'           => $imagePath,

                'is_active'       => $request->has('is_active'),

            ]);

            return redirect()
                ->route('products.index')
                ->with('success', 'Product created successfully');

        } catch (Exception $e) {

            return back()
                ->withInput()
                ->with('error', 'Failed to create product');

        }
    }

    // ================= EDIT =================
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        $categories = Category::where('is_active', 1)->get();
        $brands     = Brand::where('is_active', 1)->get();
        $units      = ProductUnit::where('is_active', 1)->get();

        return view('products.edit', compact(
            'product',
            'categories',
            'brands',
            'units'
        ));
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        try {

            $request->validate([

                'name'            => 'required|max:255',

                'sku'             => 'nullable|unique:products,sku,' . $product->id,

                'barcode'         => 'nullable|unique:products,barcode,' . $product->id,

                'category_id'     => 'nullable|exists:categories,id',

                'brand_id'        => 'nullable|exists:brands,id',

                'product_unit_id' => 'nullable|exists:product_units,id',

                'purchase_price'  => 'nullable|numeric|min:0',

                'sale_price'      => 'required|numeric|min:0',

                'stock'           => 'required|numeric|min:0',

                'alert_stock'     => 'required|numeric|min:0',

                'weight'          => 'nullable|numeric|min:0',

                'description'     => 'nullable',

                'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Validation failed: ' . $e->getMessage());
        }

        try {

            if ($request->hasFile('image')) {

                if ($product->image &&
                    Storage::disk('public')->exists($product->image)) {

                    Storage::disk('public')->delete($product->image);
                }

                $product->image = $request->file('image')
                    ->store('productsImages', 'public');
            }

            $product->update([

                'name'            => $request->name,

                'sku'             => $request->sku,

                'barcode'         => $request->barcode,

                'category_id'     => $request->category_id,

                'brand_id'        => $request->brand_id,

                'product_unit_id' => $request->product_unit_id,

                'purchase_price'  => $request->purchase_price,

                'sale_price'      => $request->sale_price,

                'stock'           => $request->stock,

                'alert_stock'     => $request->alert_stock,

                'weight'          => $request->weight,

                'description'     => $request->description,

                'is_active'       => $request->has('is_active'),

            ]);

            return redirect()
                ->route('products.index')
                ->with('success', 'Product updated successfully');

        } catch (Exception $e) {

            return back()
                ->withInput()
                ->with('error', 'Failed to update product');

        }
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        try {

            $product = Product::findOrFail($id);

            if ($product->image &&
                Storage::disk('public')->exists($product->image)) {

                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return back()->with(
                'success',
                'Product deleted successfully'
            );

        } catch (Exception $e) {

            return back()->with(
                'error',
                'Failed to delete product'
            );

        }
    }

        // ================= Increase Stock =================
    public function increaseStock(Request $request, $id)
{
    try {

        $request->validate([
            'quantity' => 'required|numeric|min:1'
        ]);

        $product = Product::findOrFail($id);

        $product->increment('stock', $request->quantity);

        return response()->json([
            'success' => true,
            'message' => 'Stock increased successfully',
            'new_stock' => $product->stock
        ]);

    } catch (Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Failed to increase stock'
        ], 500);
    }
}
}
