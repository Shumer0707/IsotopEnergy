<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['name_ru', 'name_ro', 'name_en'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }
}
