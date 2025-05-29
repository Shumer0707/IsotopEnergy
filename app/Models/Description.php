<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Description extends Model
{
  use HasFactory, Searchable;

  protected static function booted()
  {
    static::saved(fn($model) => $model->searchable());
    static::deleted(fn($model) => $model->unsearchable());
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function toSearchableArray()
  {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'language' => $this->language,
    ];
  }
}
