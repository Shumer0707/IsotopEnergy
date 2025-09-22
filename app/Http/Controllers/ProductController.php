<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ProductController extends Controller
{
  public function show(string $locale, Product $product, Request $request,)
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

    // для seo
    app()->setLocale($locale);

    $seoData = [
      'title' => __('messages.cart_title') . ' - IsotopEnergy',
      'description' => 'Корзина покупок - IsotopEnergy термопанели',
      'canonical' => $request->url(),
    ];

    view()->share('seoData', $seoData);
    view()->share('locale', $locale);

    return Inertia::render('Product', [
      'product' => $product,
      'category' => $product->category,
      'parentCategory' => $product->category?->parent,
    ]);
  }
}
