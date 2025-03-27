<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductAttribute;
use App\Models\AttributeValue;
use App\Models\Description;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function getIndexData(?int $categoryId = null): array
    {
        $productsQuery = Product::with(['category', 'descriptions', 'brand']);

        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        $products = $productsQuery->get()->map(function ($product) {
            $product->attributeValues = $product->attributeValues()->get();
            $product->images = $product->images()->get();
            return $product;
        });

        return [
            'products' => $products,
            'categories' => Category::all(),
            'brands' => Brand::all(),
            'attributes' => ProductAttribute::all(),
            'values' => AttributeValue::all(),
        ];
    }

    public function store(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // 1. Создаём товар
            $product = Product::create([
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'] ?? null,
                'price' => $data['price'],
                'discount_price' => $data['discount_price'] ?? null,
                'currency' => $data['currency'],
                'main_image' => null,
            ]);

            // 2. Создаём описания
            foreach (['ru', 'ro', 'en'] as $lang) {
                Description::create([
                    'product_id' => $product->id,
                    'language' => $lang,
                    'title' => trim($data['descriptions'][$lang]['title']),
                    'short_description' => trim($data['descriptions'][$lang]['short_description']),
                    'full_description' => trim($data['descriptions'][$lang]['full_description'] ?? ''),
                ]);
            }

            // 3. Привязываем атрибуты
            foreach ($data['attributes'] ?? [] as $attr) {
                ProductAttributeValue::create([
                    'product_id' => $product->id,
                    'attribute_id' => $attr['attribute_id'],
                    'attribute_value_id' => $attr['value_id'],
                ]);
            }

            return $product;
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            // 1. Обновляем сам товар
            $product->update([
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'] ?? null,
                'price' => $data['price'],
                'discount_price' => $data['discount_price'] ?? null,
                'currency' => $data['currency'],
            ]);

            // 2. Обновляем описания
            foreach (['ru', 'ro', 'en'] as $lang) {
                $product->descriptions()->updateOrCreate(
                    ['language' => $lang],
                    [
                        'title' => trim($data['descriptions'][$lang]['title']),
                        'short_description' => trim($data['descriptions'][$lang]['short_description']),
                        'full_description' => trim($data['descriptions'][$lang]['full_description'] ?? ''),
                    ]
                );
            }

            // 3. Обновляем атрибуты
            $product->attributeValues()->delete();

            foreach ($data['attributes'] ?? [] as $attr) {
                ProductAttributeValue::create([
                    'product_id' => $product->id,
                    'attribute_id' => $attr['attribute_id'],
                    'attribute_value_id' => $attr['value_id'],
                ]);
            }

            return $product;
        });
    }

    public function destroy($product)
    {
        return $product->delete();
    }
}
