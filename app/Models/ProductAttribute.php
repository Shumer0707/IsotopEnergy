<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    public function translations()
    {
        return $this->hasMany(AttributeTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(AttributeTranslation::class)
            ->where('language', app()->getLocale());
    }

    // Фоллбэк
    public function translatedName()
    {
        return $this->translations
            ->firstWhere('language', app()->getLocale())
            ?->name
            ?? $this->translations->firstWhere('language', config('app.fallback_locale'))?->name;
    }

    public function values()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }
}
