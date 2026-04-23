<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
  use Notifiable;

  protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'category_id'
  ];
  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function tickets()
  {
    return $this->hasMany(Ticket::class);
  }

  public function assignedTickets()
  {
    return $this->hasMany(Ticket::class, 'agent_id');
  }

  public function comments()
  {
    return $this->hasMany(Comment::class);
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function isUser()
  {
    return $this->role == 'user';
  }

  public function isAgent()
  {
    return $this->role == 'agent';
  }

  public function isAdmin()
  {
    return $this->role == 'admin';
  }
}
