<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
  protected $fillable = [
    'title',
    'description',
    'status',
    'priority',
    'solved_by_ai',
    'category_id',
    'user_id',
    'agent_id',
    'file_path',
  ];

  protected $casts = [
    'solved_by_ai' => 'boolean',
  ];

  public static function statuses()
  {
    return ['open', 'in_progress', 'resolved', 'closed'];
  }

  public static function priorities()
  {
    return ['low', 'medium', 'high'];
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function agent()
  {
    return $this->belongsTo(User::class, 'agent_id');
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function comments()
  {
    return $this->hasMany(Comment::class);
  }
}
