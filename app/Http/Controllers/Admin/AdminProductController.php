<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductAttribute;
use App\Models\AttributeValue;
use App\Models\Description;
use App\Models\Image;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Products/IndexProducts', [
            'products' => Product::with(['category', 'descriptions', 'brand'])->get()->map(function ($product) {
                $product->attributeValues = $product->attributeValues()->get();
                $product->images = $product->images()->get();
                return $product;
            }),
            'categories' => Category::all(),
            'brands' => \App\Models\Brand::all(),
            'attributes' => ProductAttribute::all(),
            'values' => AttributeValue::all(),
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'currency' => 'required|string|size:3',
            'descriptions' => 'required|array',
            'descriptions.ru.title' => 'required|string|max:255',
            'descriptions.ru.short_description' => 'required|string',
            'descriptions.ro.title' => 'required|string|max:255',
            'descriptions.ro.short_description' => 'required|string',
            'descriptions.en.title' => 'required|string|max:255',
            'descriptions.en.short_description' => 'required|string',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_id' => 'required|exists:product_attributes,id',
            'attributes.*.value_id' => 'required|exists:attribute_values,id',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'price' => $validated['price'],
                'discount_price' => $validated['discount_price'] ?? null,
                'currency' => $validated['currency'],
                'main_image' => null, // Добавим позже через выбор из загруженных фото
            ]);

            foreach (['ru', 'ro', 'en'] as $lang) {
                Description::create([
                    'product_id' => $product->id,
                    'language' => $lang,
                    'title' => trim($validated['descriptions'][$lang]['title']),
                    'short_description' => trim($validated['descriptions'][$lang]['short_description']),
                    'full_description' => trim($validated['descriptions'][$lang]['full_description'] ?? ''),
                ]);
            }

            foreach ($validated['attributes'] ?? [] as $attr) {
                ProductAttributeValue::create([
                    'product_id' => $product->id,
                    'attribute_id' => $attr['attribute_id'],
                    'attribute_value_id' => $attr['value_id'],
                ]);
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Товар успешно добавлен!');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'currency' => 'required|string|size:3',
            'descriptions' => 'required|array',
            'descriptions.ru.title' => 'required|string|max:255',
            'descriptions.ru.short_description' => 'required|string',
            'descriptions.ro.title' => 'required|string|max:255',
            'descriptions.ro.short_description' => 'required|string',
            'descriptions.en.title' => 'required|string|max:255',
            'descriptions.en.short_description' => 'required|string',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_id' => 'required|exists:product_attributes,id',
            'attributes.*.value_id' => 'required|exists:attribute_values,id',
        ]);

        DB::transaction(function () use ($validated, $product) {
            $product->update([
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'price' => $validated['price'],
                'discount_price' => $validated['discount_price'] ?? null,
                'currency' => $validated['currency'],
            ]);

            foreach (['ru', 'ro', 'en'] as $lang) {
                $product->descriptions()->updateOrCreate(
                    ['language' => $lang],
                    [
                        'title' => trim($validated['descriptions'][$lang]['title']),
                        'short_description' => trim($validated['descriptions'][$lang]['short_description']),
                        'full_description' => trim($validated['descriptions'][$lang]['full_description'] ?? ''),
                    ]
                );
            }

            $product->attributeValues()->delete();
            foreach ($validated['attributes'] ?? [] as $attr) {
                ProductAttributeValue::create([
                    'product_id' => $product->id,
                    'attribute_id' => $attr['attribute_id'],
                    'attribute_value_id' => $attr['value_id'],
                ]);
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Товар обновлён!');
    }

    public function destroy(Product $product)
    {
        $product->delete(); // удалит и descriptions, и attributeValues по каскаду
        return redirect()->route('admin.products.index')->with('success', 'Товар удалён!');
    }

    public function uploadImages(Request $request, Product $product)
    {
        $request->validate([
            'images.*' => 'required|image|max:2048', // до 2MB на файл
        ]);

        foreach ($request->file('images') as $imageFile) {
            $path = $imageFile->store('products', 'public');

            Image::create([
                'product_id' => $product->id,
                'path' => $path,
            ]);
        }

        return back()->with('success', 'Изображения загружены!');
    }

    public function deleteImage(Image $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Изображение удалено!');
    }

    public function imagesList(Product $product)
    {
        return response()->json([
            'images' => $product->images()->get()
        ]);
    }
    public function setMainImage(Request $request, Product $product)
{
    $validated = $request->validate([
        'image_id' => 'required|exists:images,id',
    ]);

    $image = Image::find($validated['image_id']);

    if ($image->product_id !== $product->id) {
        abort(403, 'Это изображение не принадлежит данному товару.');
    }

    $product->update([
        'main_image' => $image->path
    ]);

    return response()->json(['success' => true]);
}
}
