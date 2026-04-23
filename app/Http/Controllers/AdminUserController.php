<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
  /**
   * Display a listing of all users (agents page).
   */
  public function index(Request $request)
  {
    $query = User::with('category');

    // Search by name
    if ($request->filled('search')) {
      $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Filter by role
    if ($request->filled('role')) {
      $query->where('role', $request->role);
    }

    $users = $query->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'agent' THEN 2 WHEN 'user' THEN 3 ELSE 4 END")
      ->orderBy('name')
      ->paginate(15)
      ->withQueryString();

    $categories = Category::orderBy('name')->get();

    return view('admin.agents', compact('users', 'categories'));
  }

  /**
   * Update user role.
   */
  public function updateRole(Request $request, User $user)
  {
    $request->validate([
      'role' => 'required|in:user,agent',
    ]);

    $newRole = $request->role;

    // If changing from agent to user, remove category assignment
    if ($newRole === 'user') {
      $user->update([
        'role' => 'user',
        'category_id' => null,
      ]);
    } else {
      $user->update([
        'role' => 'agent',
      ]);
    }

    return back()->with('success', "Role for {$user->name} updated to " . ucfirst($newRole) . " successfully.");
  }

  /**
   * Update agent's category assignment.
   */
  public function updateCategory(Request $request, User $user)
  {
    $request->validate([
      'category_id' => 'nullable|exists:categories,id',
    ]);

    $user->update([
      'category_id' => $request->category_id,
    ]);

    $categoryName = $request->category_id
      ? Category::find($request->category_id)->name
      : 'None';

    return back()->with('success', "{$user->name} assigned to category: {$categoryName}.");
  }
}
