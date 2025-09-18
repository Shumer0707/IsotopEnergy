<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
  public function show(string $locale, Product $product)
  {
    $product->load([
      'description',
      'brand',
      'category.parent',
      'images',

      // ✅ Основные варианты с атрибутами
      'variants' => function ($query) {
        $query->with([
          'variantAttributes.attribute.translations',
          'variantAttributes.attributeValue.translations'
        ])->orderBy('price', 'asc');
      },

      // ✅ Специальные варианты
      'defaultVariant.variantAttributes.attribute.translations',
      'defaultVariant.variantAttributes.attributeValue.translations',
      'cheapestVariant',

      'promotion.discountGroup',
    ]);

    return Inertia::render('Product', [
      'product' => $product,
      'category' => $product->category,
      'parentCategory' => $product->category?->parent,
    ]);
  }
}
