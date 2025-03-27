<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\ProductAttribute;
use App\Http\Requests\Admin\StoreAttributeRequest;
use App\Http\Requests\Admin\UpdateAttributeRequest;
use App\Services\AttributeService;

class AdminAttributeController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Attributes/IndexAttributes', [
            'attributes' => ProductAttribute::all()
        ]);
    }

    public function store(StoreAttributeRequest $request, AttributeService $service)
    {
        $service->store($request->validated());

        return redirect()->route('admin.attributes.index')->with('success', 'Атрибут успешно добавлен!');
    }

    public function update(UpdateAttributeRequest $request, ProductAttribute $attribute, AttributeService $service)
    {
        $service->update($attribute, $request->validated());

        return redirect()->route('admin.attributes.index')->with('success', 'Атрибут обновлён!');
    }

    public function destroy(ProductAttribute $attribute)
    {
        $attribute->delete();

        return redirect()->route('admin.attributes.index')->with('success', 'Атрибут удалён!');
    }
}
