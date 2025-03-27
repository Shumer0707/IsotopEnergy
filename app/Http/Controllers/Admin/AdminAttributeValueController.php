<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\AttributeValue;
use App\Http\Requests\Admin\StoreAttributeValueRequest;
use App\Http\Requests\Admin\UpdateAttributeValueRequest;
use App\Services\AttributeValueService;
use Illuminate\Http\Request;

class AdminAttributeValueController extends Controller
{
    public function index(Request $request, AttributeValueService $service)
    {
        return Inertia::render(
            'Admin/AttributeValues/IndexAttributeValues',
            $service->getIndexData($request->query('attribute'))
        );
    }

    public function store(StoreAttributeValueRequest $request, AttributeValueService $service)
    {
        $service->store($request->validated());

        return redirect()->route('admin.attribute-values.index')->with('success', 'Значение добавлено!');
    }

    public function update(UpdateAttributeValueRequest $request, AttributeValue $attributeValue, AttributeValueService $service)
    {
        $service->update($attributeValue, $request->validated());

        return redirect()->route('admin.attribute-values.index')->with('success', 'Значение обновлено!');
    }

    public function destroy(AttributeValue $attributeValue)
    {
        $attributeValue->delete();

        return redirect()->route('admin.attribute-values.index')->with('success', 'Значение удалено!');
    }
}
