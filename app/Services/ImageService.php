<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function upload(Product $product, array $images): void
    {
        foreach ($images as $imageFile) {
            /** @var UploadedFile $imageFile */
            $path = $imageFile->store('products', 'public');

            Image::create([
                'product_id' => $product->id,
                'path' => $path,
            ]);
        }
    }

    public function delete(Image $image): void
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();
    }

    public function setMain(Product $product, int $imageId): void
    {
        $image = Image::findOrFail($imageId);

        if ($image->product_id !== $product->id) {
            abort(403, 'Это изображение не принадлежит данному товару.');
        }

        $product->update([
            'main_image' => $image->path
        ]);
    }
}
