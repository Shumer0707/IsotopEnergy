<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
  use HasFactory;

  protected $fillable = ['product_id', 'attribute_id', 'attribute_value_id'];

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function attribute()
  {
    return $this->belongsTo(ProductAttribute::class, 'attribute_id');
  }

  public function value()
  {
    return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
  }
}
