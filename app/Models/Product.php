<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Product extends Model
{
  use HasFactory;

  protected $fillable = [
    'category_id',
    'brand_id',
    'price',
    'discount_price',
    'currency',
    'main_image'
  ];

  protected $appends = ['discounted_price'];
  // ✅ Описание на текущем языке
  public function description()
  {
    return $this->hasOne(Description::class)
      ->whereIn('language', [App::getLocale(), config('app.fallback_locale')])
      ->orderByRaw("FIELD(language, ?, ?)", [App::getLocale(), config('app.fallback_locale')]);
  }

  // ✅ Все описания (по умолчанию Laravel)
  public function descriptions()
  {
    return $this->hasMany(Description::class);
  }
  public function brand()
  {
    return $this->belongsTo(Brand::class);
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function attributes()
  {
    return $this->belongsToMany(ProductAttribute::class, 'product_attribute_values')
      ->withPivot('attribute_value_id');
  }

  public function images()
  {
    return $this->hasMany(Image::class);
  }
  public function attributeValues()
  {
    return $this->hasMany(ProductAttributeValue::class, 'product_id', 'id');
  }

  public function promotion()
  {
    return $this->hasOne(Promotion::class);
  }

  public function getDiscountedPriceAttribute()
{
    $discount = $this->promotion?->discountGroup?->discount_percent ?? 0;
    $discounted = $this->price * (1 - $discount / 100);

    return number_format(floor($discounted), 2, '.', '');
}
}
