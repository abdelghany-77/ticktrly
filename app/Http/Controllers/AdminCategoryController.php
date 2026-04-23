<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
  /**
   * Display all categories with agent count.
   */
  public function index()
  {
    $categories = Category::withCount(['users' => function ($query) {
      $query->where('role', 'agent');
    }])->orderBy('name')->get();

    return view('admin.categories', compact('categories'));
  }

  /**
   * Store a new category.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:categories,name',
      'description' => 'nullable|string|max:1000',
    ]);

    Category::create([
      'name' => $request->name,
      'description' => $request->description,
    ]);

    return back()->with('success', "Category '{$request->name}' created successfully.");
  }

  /**
   * Delete a category.
   */
  public function destroy(Category $category)
  {
    $name = $category->name;
    $category->delete();

    return back()->with('success', "Category '{$name}' deleted successfully.");
  }
}
