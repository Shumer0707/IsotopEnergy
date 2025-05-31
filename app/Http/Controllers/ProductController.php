<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
  public function show(Product $product)
  {
    $product->load([
      'description',
      'brand',
      'category.parent',
      'images',
      'attributeValues.attribute.translations',
      'attributeValues.value.translations',
    ]);

    return Inertia::render('Product', [
      'product' => $product,
      'category' => $product->category, // подкатегория
      'parentCategory' => $product->category?->parent, // родитель
    ]);
  }
}
