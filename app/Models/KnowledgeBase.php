<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
  protected $fillable = [
    'category_id',
    'question',
    'answer',
    'keywords',
  ];

  public function category()
  {
    return $this->belongsTo(Category::class);
  }
}
