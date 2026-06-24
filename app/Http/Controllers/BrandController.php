<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
       // ================= INDEX =================
    public function index()
    {
        $brands = Brand::orderBy('is_active', 'desc')->get();

        return view('brands.index', compact('brands'));
    }
    public function edit($id)
{
    $brand = Brand::findOrFail($id);

    return response()->json($brand);
}

 // ================= STORE =================
public function store(Request $request)
{
    try {

        $request->validate([
            'name'        => 'required|unique:brands,name',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable',
            'is_active'   => 'boolean',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {

            // store in: storage/app/public/brandsImages
            $imagePath = $request->file('image')
                ->store('brandsImages', 'public');
        }

        Brand::create([
            'name'        => $request->name,
            'image'       => $imagePath,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Brand created successfully',
        ]);

    } catch (ValidationException $e) {

        return response()->json([
            'status' => 'error',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Exception $e) {

        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}


// ================= UPDATE =================
public function update(Request $request, $id)
{
    try {

        $brand = Brand::findOrFail($id);

        $request->validate([
            'name'        => 'required|unique:brands,name,' . $brand->id,
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable',
            'is_active'   => 'boolean',
        ]);

        if ($request->hasFile('image')) {

            // delete old image if exists
            if ($brand->image && Storage::disk('public')->exists($brand->image)) {
                Storage::disk('public')->delete($brand->image);
            }

            $brand->image = $request->file('image')
                ->store('brandsImages', 'public');
        }

        $brand->name        = $request->name;
        $brand->description = $request->description;
        $brand->is_active   = $request->boolean('is_active');

        $brand->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Brand updated successfully',
        ]);

    } catch (ValidationException $e) {

        return response()->json([
            'status' => 'error',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Exception $e) {

        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}
    // ================= DELETE =================


public function destroy($id)
{
    try {

        $brand = Brand::findOrFail($id);

        // ================= DELETE IMAGE FILE =================
        if ($brand->image && Storage::disk('public')->exists($brand->image)) {
            Storage::disk('public')->delete($brand->image);
        }

        // ================= DELETE DATABASE ROW =================
        $brand->delete();

return back()->with('success', 'Brand deleted successfully');

    } catch (Exception $e) {

        return back()->with('error', 'Error deleting brand: ' . $e->getMessage());

    }
}
}
