<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = ['attribute_id'];


    public function translations()
    {
        return $this->hasMany(AttributeValueTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(AttributeValueTranslation::class)
            ->where('language', app()->getLocale());
    }

    public function translatedValue()
    {
        return $this->translations
            ->firstWhere('language', app()->getLocale())
            ?->value
            ?? $this->translations->firstWhere('language', config('app.fallback_locale'))?->value;
    }

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }
}
