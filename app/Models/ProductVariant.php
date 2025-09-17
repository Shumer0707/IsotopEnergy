<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
  use HasFactory;

  protected $fillable = [
    'product_id',
    'sku',
    'price',
    'is_default'
  ];

  protected $casts = [
    'price' => 'decimal:2',
    'is_default' => 'boolean'
  ];

  // ✅ СВЯЗИ

  // Основной товар
  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  // Атрибуты этого варианта (через промежуточную таблицу)
  public function attributeValues()
  {
    return $this->belongsToMany(
      AttributeValue::class,
      'product_variant_attributes',
      'variant_id',
      'attribute_value_id'
    )->withPivot('attribute_id')->withTimestamps();
  }

  // Промежуточные записи (для более детального контроля)
  public function variantAttributes()
  {
    return $this->hasMany(ProductVariantAttribute::class, 'variant_id');
  }

  // ✅ МЕТОДЫ

  // Получить атрибуты варианта в удобном формате
  public function getAttributesFormatted()
  {
    return $this->variantAttributes()
      ->with(['attribute.translations', 'attributeValue.translations'])
      ->get()
      ->map(function ($item) {
        return [
          'attribute_name' => $item->attribute->translatedName(),
          'value_name' => $item->attributeValue->translatedValue(),
          'attribute_id' => $item->attribute_id,
          'value_id' => $item->attribute_value_id
        ];
      });
  }

  // Генерация SKU на основе базового артикула и атрибутов
  public function generateSku()
  {
    $baseSku = $this->product->base_sku ?? $this->product->id;

    $attributeParts = $this->variantAttributes()
      ->with(['attribute.translations', 'attributeValue.translations'])
      ->get()
      ->map(function ($item) {
        // Берем первые буквы названия атрибута + значение
        $attrName = $item->attribute->translatedName();
        $valueName = $item->attributeValue->translatedValue();

        // Очищаем от спецсимволов и делаем короче
        $attrShort = preg_replace('/[^a-zA-Zа-яА-Я0-9]/u', '', $attrName);
        $valueShort = preg_replace('/[^a-zA-Zа-яА-Я0-9]/u', '', $valueName);

        return strtolower($attrShort . '_' . $valueShort);
      })
      ->implode('-');

    return $baseSku . ($attributeParts ? '-' . $attributeParts : '');
  }

  // Автогенерация SKU при сохранении
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($variant) {
      if (empty($variant->sku)) {
        // SKU сгенерируется после создания связей с атрибутами
        $variant->sku = 'temp_' . time() . '_' . rand(1000, 9999);
      }
    });

    static::created(function ($variant) {
      // После создания связей с атрибутами обновляем SKU
      if (str_starts_with($variant->sku, 'temp_')) {
        $variant->sku = $variant->generateSku();
        $variant->saveQuietly(); // Сохраняем без событий, чтобы избежать рекурсии
      }
    });
  }

  // ✅ SCOPES (для удобных запросов)

  // Только дефолтные варианты
  public function scopeDefault($query)
  {
    return $query->where('is_default', true);
  }

  // Сортировка по цене
  public function scopeOrderByPrice($query, $direction = 'asc')
  {
    return $query->orderBy('price', $direction);
  }

  // Поиск по диапазону цен
  public function scopePriceBetween($query, $min, $max)
  {
    return $query->whereBetween('price', [$min, $max]);
  }
}
