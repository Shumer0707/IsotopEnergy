<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'discount_percent'];

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }
}
