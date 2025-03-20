<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Description extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'language', 'text'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
