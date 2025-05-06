<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'discount_group_id',
        'starts_at',
        'ends_at',
        'active'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function discountGroup()
    {
        return $this->belongsTo(DiscountGroup::class);
    }
}
