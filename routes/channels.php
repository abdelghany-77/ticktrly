<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Agent personal channel — only the agent themselves can listen
Broadcast::channel('agent.{id}', function (User $user, int $id) {
  return $user->id === $id && ($user->role === 'agent' || $user->role === 'admin');
});

// User personal channel — only the user themselves can listen
Broadcast::channel('user.{id}', function (User $user, int $id) {
  return $user->id === $id;
});

// Category channel — agents in that category can listen
Broadcast::channel('category.{categoryId}', function (User $user, int $categoryId) {
  return $user->role === 'agent' && $user->category_id === $categoryId;
});

// Admins channel — all admins can listen for new ticket notifications
Broadcast::channel('admins', function (User $user) {
  return $user->role === 'admin';
});
