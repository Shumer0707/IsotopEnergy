<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeTranslation extends Model
{
    protected $fillable = ['product_attribute_id', 'language', 'name'];

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }
}
