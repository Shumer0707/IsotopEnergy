<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Product;
use App\Services\ImageService;
use App\Http\Requests\Admin\UploadImageRequest;
use App\Http\Requests\Admin\SetMainImageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AdminImageController extends Controller
{
    public function uploadImages(UploadImageRequest $request, Product $product, ImageService $service)
    {
        $service->upload($product, $request->file('images'));

        return back()->with('success', 'Изображения загружены!');
    }

    public function deleteImage(Image $image, ImageService $service)
    {
        $service->delete($image);

        return back()->with('success', 'Изображение удалено!');
    }

    public function imagesList(Product $product)
    {
        return response()->json([
            'images' => $product->images()->get()
        ]);
    }

    public function setMainImage(SetMainImageRequest $request, Product $product, ImageService $service)
    {
        $service->setMain($product, $request->validated('image_id'));

        return response()->json(['success' => true]);
    }
}

