<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = ['attribute_id', 'value_ru', 'value_ro', 'value_en'];

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }
}
