<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductVariant;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\Product\ProductFilterService;

class CategoryController extends Controller
{
  public function show(Request $request, string $locale, Category $category, ProductFilterService $filterService)
  {
    $category->load([
      'children',
      'translation',
      'parent.translation',
      'parent.children.translation',
    ]);

    $brands = Brand::select('id', 'name')->get();

    $max_price = (int) ceil((
      ProductVariant::whereHas('product', function ($query) use ($category) {
        $query->where('category_id', $category->id);
      })->max('price') ?? 10000
    ) / 1000) * 1000;

    // фильтры из JSON
    $raw = $request->input('filters', '');
    $decoded = is_string($raw) && $raw !== '' ? json_decode($raw, true) : [];
    $filters = is_array($decoded) ? $decoded : [];

    $sort = $request->input('sort');

    // нормализация цены
    $from = isset($filters['price_from']) ? (int)$filters['price_from'] : 0;
    $to   = isset($filters['price_to'])   ? (int)$filters['price_to']   : $max_price;
    $from = max(0, min($from, $max_price));
    $to   = max(0, min($to,   $max_price));
    if ($from > $to) [$from, $to] = [$to, $from];

    // бренды → числа
    $brandsFilter = [];
    if (!empty($filters['brands'])) {
      $brandsFilter = array_values(array_filter(array_map('intval', (array)$filters['brands']), fn($v) => $v > 0));
    }

    // attrs → {attrId: number[]}
    $attrs = [];
    if (!empty($filters['attrs']) && is_array($filters['attrs'])) {
      foreach ($filters['attrs'] as $attrId => $valueIds) {
        $aId = (int)$attrId;
        if ($aId <= 0) continue;
        $vals = array_values(array_filter(array_map('intval', (array)$valueIds), fn($v) => $v > 0));
        if ($vals) $attrs[$aId] = array_values(array_unique($vals));
      }
    }

    $normFilters = [
      'brands'     => $brandsFilter,
      'attrs'      => $attrs,
      'price_from' => $from,
      'price_to'   => $to,
    ];

    // ✅ ИЗМЕНЕНИЕ: Получаем результат фильтрации и добавляем нужные связи
    $productsQuery = $filterService->filter($category, $normFilters, $sort);

    // ✅ ДОБАВЛЯЕМ: Загружаем все нужные связи для корректного отображения карточек
    $products = $productsQuery->with([
      'description',
      'brand',
      'images',

      // Варианты с атрибутами и переводами
      'variants' => function ($query) {
        $query->with([
          'variantAttributes.attribute.translations',
          'variantAttributes.attributeValue.translations'
        ])->orderBy('price', 'asc');
      },

      // Дефолтный вариант с атрибутами
      'defaultVariant' => function ($query) {
        $query->with([
          'variantAttributes.attribute.translations',
          'variantAttributes.attributeValue.translations'
        ]);
      },

      // Самый дешевый вариант
      'cheapestVariant' => function ($query) {
        $query->with([
          'variantAttributes.attribute.translations',
          'variantAttributes.attributeValue.translations'
        ]);
      },

      'promotion.discountGroup',
    ])->paginate(20)->withQueryString();

    // доступные фильтры для рендера UI
    $available = $filterService->availableFilters($category);

    // для seo
    app()->setLocale($locale);

    $seoData = [
      'title' => __('messages.cart_title') . ' - IsotopEnergy',
      'description' => 'Корзина покупок - IsotopEnergy термопанели',
      'canonical' => $request->url(),
    ];

    view()->share('seoData', $seoData);
    view()->share('locale', $locale);

    return Inertia::render('Products/ProductsByCategory', [
      'category'          => $category,
      'parentCategory'    => $category->parent,
      'products'          => $products, // ✅ ИЗМЕНЕНИЕ: Теперь передаем уже загруженные данные
      'brands'            => $brands,
      'max_price'         => $max_price,
      'filters'           => $normFilters,
      'sort'              => $sort,
      'available_filters' => $available,
    ]);
  }
}
