<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('master.category.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'code' => 'nullable|string|max:50|unique:categories,code',
            'description' => 'nullable|string|max:1000',
        ]);

        Category::create($request->only('name', 'code', 'description'));
        return redirect()->route('categories.index')->with('success', 'Category added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'code' => 'nullable|string|max:50|unique:categories,code,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($request->only('name', 'code', 'description'));
        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->status = 0; // inactive // or 0 if you use integer
        $category->save();

        // $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
