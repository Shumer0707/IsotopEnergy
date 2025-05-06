<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Category;
use App\Services\CategoryService;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;

class AdminCategoryController extends Controller
{
  public function index(CategoryService $service)
  {
    return Inertia::render('Admin/Categories/IndexCategory', $service->getIndexData(
      request('filter'),
      request('parent_id')
    ));
  }

  public function store(StoreCategoryRequest $request, CategoryService $service)
  {
    $service->store($request->validated());

    return redirect()->route('admin.categories.index')->with('success', 'Категория успешно добавлена!');
  }

  public function update(UpdateCategoryRequest $request, Category $category, CategoryService $service)
  {
    $service->update($category, $request->validated());

    return redirect()->route('admin.categories.index')->with('success', 'Категория обновлена!');
  }

  public function destroy(Category $category, CategoryService $service)
  {
    $service->delete($category);

    return redirect()->route('admin.categories.index')->with('success', 'Категория удалена!');
  }
}
