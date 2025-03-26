<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function descriptions()
    {
        return $this->hasMany(Description::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id', 'id');
    }
}
