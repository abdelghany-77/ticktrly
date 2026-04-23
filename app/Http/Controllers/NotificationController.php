<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
  public function index(): JsonResponse
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $notifications = $user->notifications()
      ->latest()
      ->take(30)
      ->get()
      ->map(function ($notification) {
        return [
          'id' => $notification->id,
          'title' => $notification->data['title'] ?? '',
          'body' => $notification->data['body'] ?? '',
          'url' => $notification->data['url'] ?? '#',
          'type' => $notification->data['type'] ?? '',
          'read' => !is_null($notification->read_at),
          'time' => $notification->created_at->toIso8601String(),
        ];
      });

    $unreadCount = $user->unreadNotifications()->count();

    return response()->json([
      'notifications' => $notifications,
      'unread_count' => $unreadCount,
    ]);
  }

  /**
   * Mark all notifications as read.
   */
  public function markAllRead(): JsonResponse
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $user->unreadNotifications->markAsRead();

    return response()->json(['success' => true]);
  }

  public function markAsRead(string $id): JsonResponse
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $notification = $user->notifications()->find($id);

    if ($notification) {
      $notification->markAsRead();
    }

    return response()->json(['success' => true]);
  }
}
