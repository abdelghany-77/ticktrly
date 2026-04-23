<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return Auth::check()
    ? redirect()->route('dashboard')
    : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
  Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
  Route::post('/login', [AuthController::class, 'login'])->name('login.store');

  Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
  Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
  Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

  Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
  Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
  Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

  Route::get('/tickets/activity', [TicketController::class, 'activity'])->name('tickets.activity');
  Route::patch('/tickets/{ticket}/priority', [TicketController::class, 'updatePriority'])->name('tickets.update-priority');
  Route::patch('/tickets/{ticket}/assign-agent', [TicketController::class, 'assignAgent'])->name('tickets.assign-agent');
  Route::resource('tickets', TicketController::class);
  Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])->name('comments.store');

  Route::post('/tickets/ai-search', [TicketController::class, 'aiSearch'])->name('tickets.ai-search');

  // Admin routes
  Route::get('/admin/agents', [AdminUserController::class, 'index'])->name('admin.agents.index');
  Route::patch('/admin/agents/{user}/role', [AdminUserController::class, 'updateRole'])->name('admin.agents.update-role');
  Route::patch('/admin/agents/{user}/category', [AdminUserController::class, 'updateCategory'])->name('admin.agents.update-category');

  Route::get('/admin/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
  Route::post('/admin/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
  Route::delete('/admin/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');
});
