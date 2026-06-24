<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
     // LIST
    public function index()
    {
        $categories = Category::orderBy('is_active', 'desc')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }
        // STORE (AJAX)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'description' => 'nullable',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'category' => $category
        ]);
    }

    // EDIT DATA (AJAX)
    public function edit($id)
    {
        return response()->json(Category::findOrFail($id));
    }

    // UPDATE (AJAX)
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:categories,name,' . $id,
            'description' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
        ]);
    }

    // DELETE (AJAX ready)
    public function destroy($id)
    {
        try{
  Category::findOrFail($id)->delete();

         return back()->with('success', 'Category deleted successfully');
        }catch(Exception $e){
               return back()->with('error', 'Failed to delete category. It may be associated with other records.');
        }

    }
}
