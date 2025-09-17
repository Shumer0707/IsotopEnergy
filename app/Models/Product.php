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
    'base_sku',        // Базовый артикул для генерации SKU вариантов
    'measurement',
    'currency',
    'main_image'
  ];

  // Убираем 'discounted_price' из appends, так как цены теперь в вариантах
  // protected $appends = ['discounted_price'];

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

  // ✅ НОВЫЕ СВЯЗИ ДЛЯ ВАРИАНТОВ

  // Все варианты товара
  public function variants()
  {
    return $this->hasMany(ProductVariant::class);
  }

  // Основной (дефолтный) вариант
  public function defaultVariant()
  {
    return $this->hasOne(ProductVariant::class)->where('is_default', true);
  }

  // Самый дешевый вариант
  public function cheapestVariant()
  {
    return $this->hasOne(ProductVariant::class)->orderBy('price', 'asc');
  }

  // Самый дорогой вариант
  public function expensiveVariant()
  {
    return $this->hasOne(ProductVariant::class)->orderBy('price', 'desc');
  }

  // ✅ СТАРЫЕ СВЯЗИ (пока оставляем для совместимости)
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

  // Связь с акциями (пока оставляем)
  public function promotion()
  {
    return $this->hasOne(Promotion::class);
  }

  // ✅ НОВЫЕ МЕТОДЫ ДЛЯ РАБОТЫ С ВАРИАНТАМИ

  // Получить диапазон цен товара
  public function getPriceRange()
  {
    $prices = $this->variants()->pluck('price');

    if ($prices->isEmpty()) {
      return null;
    }

    $min = $prices->min();
    $max = $prices->max();

    return $min == $max ?
      number_format($min, 2) :
      number_format($min, 2) . ' - ' . number_format($max, 2);
  }

  // Установить дефолтный вариант (самый дешевый)
  public function setDefaultVariant()
  {
    // Сбрасываем все дефолтные варианты
    $this->variants()->update(['is_default' => false]);

    // Находим самый дешевый и делаем его дефолтным
    $cheapest = $this->variants()->orderBy('price', 'asc')->first();

    if ($cheapest) {
      $cheapest->update(['is_default' => true]);
    }

    return $cheapest;
  }

  // Проверка - есть ли варианты у товара
  public function hasVariants()
  {
    return $this->variants()->exists();
  }
}
