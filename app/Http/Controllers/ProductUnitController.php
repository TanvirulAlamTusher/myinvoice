<?php

namespace App\Http\Controllers;

use App\Models\ProductUnit;
use Exception;
use Illuminate\Http\Request;

class ProductUnitController extends Controller
{
     // ================= INDEX =================
    public function index()
    {
        $units = ProductUnit::orderBy('is_active', 'desc')->get();
        return view('product_units.index', compact('units'));
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|unique:product_units,name',
                'is_active' => 'boolean',
            ]);

            $unit = ProductUnit::create([
                'name' => $request->name,
                'is_active' => $request->boolean('is_active'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product unit created successfully',
                'data' => $unit
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create product unit'
            ], 500);
        }
    }

    // ================= EDIT =================
    public function edit($id)
    {
        try {

            $unit = ProductUnit::findOrFail($id);

            return response()->json($unit);

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Product unit not found'
            ], 404);
        }
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        try {

            $unit = ProductUnit::findOrFail($id);

            $request->validate([
                'name' => 'required|unique:product_units,name,' . $unit->id,
                'is_active' => 'required|in:0,1',
            ]);

            $unit->update([
                'name' => $request->name,
                'is_active' => (bool) $request->is_active,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product unit updated successfully',
                'data' => $unit
            ]);

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update product unit'
            ], 500);
        }
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        try {

            $unit = ProductUnit::findOrFail($id);
            $unit->delete();

          return back()->with('success', 'Product unit deleted successfully');
        } catch (Exception $e) {

            return back()->with('error', 'Failed to delete product unit');
        }
    }
}
