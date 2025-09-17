<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantAttribute extends Model
{
  use HasFactory;

  protected $fillable = [
    'variant_id',
    'attribute_id',
    'attribute_value_id'
  ];

  // ✅ СВЯЗИ

  // Вариант товара, к которому относится этот атрибут
  public function variant()
  {
    return $this->belongsTo(ProductVariant::class, 'variant_id');
  }

  // Атрибут (толщина, плотность и т.д.)
  public function attribute()
  {
    return $this->belongsTo(ProductAttribute::class, 'attribute_id');
  }

  // Значение атрибута (50мм, 15кг/м³ и т.д.)
  public function attributeValue()
  {
    return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
  }

  // ✅ МЕТОДЫ

  // Получить название атрибута на текущем языке
  public function getAttributeName()
  {
    return $this->attribute?->translatedName();
  }

  // Получить значение атрибута на текущем языке
  public function getAttributeValueName()
  {
    return $this->attributeValue?->translatedValue();
  }

  // Получить полную строку "Атрибут: Значение"
  public function getFormattedString()
  {
    return $this->getAttributeName() . ': ' . $this->getAttributeValueName();
  }

  // ✅ SCOPES

  // Поиск по конкретному атрибуту
  public function scopeByAttribute($query, $attributeId)
  {
    return $query->where('attribute_id', $attributeId);
  }

  // Поиск по конкретному значению атрибута
  public function scopeByAttributeValue($query, $attributeValueId)
  {
    return $query->where('attribute_value_id', $attributeValueId);
  }

  // Поиск по варианту товара
  public function scopeByVariant($query, $variantId)
  {
    return $query->where('variant_id', $variantId);
  }
}
