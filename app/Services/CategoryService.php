<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
  public function getIndexData(?string $filter, ?int $parentId): array
  {
    $query = Category::query();

    if ($filter === 'root') {
      $query->whereNull('parent_id');
    } elseif ($filter === 'children' && $parentId) {
      $query->where('parent_id', $parentId);
    }

    return [
      'categories' => $query->with('translations')->get(),
      'parentCategories' => Category::with('translation')->whereNull('parent_id')->get(),
      'filters' => [
        'filter' => $filter,
        'parent_id' => $parentId,
      ],
    ];
  }

  public function store(array $data): Category
  {
    $logoPath = null;

    if (isset($data['logo'])) {
      $logoPath = $data['logo']->store('categories', 'public');
    }

    $category = Category::create([
      'parent_id' => $data['parent_id'] ?? null,
      'logo' => $logoPath
    ]);

    foreach ($data['translations'] as $lang => $name) {
      $category->translations()->create([
        'language' => $lang,
        'name' => mb_convert_case(trim($name), MB_CASE_TITLE, 'UTF-8'),
      ]);
    }

    return $category;
  }

  public function update(Category $category, array $data): Category
  {
    // 🔹 Обработка логотипа
    if (isset($data['logo'])) {
      if ($category->logo && Storage::disk('public')->exists($category->logo)) {
        Storage::disk('public')->delete($category->logo);
      }

      $data['logo'] = $data['logo']->store('categories', 'public');
    } else {
      unset($data['logo']); // чтобы не затирать старое значение
    }

    $category->update([
      'parent_id' => $data['parent_id'] ?? null,
      'logo' => $data['logo'] ?? $category->logo,
    ]);

    foreach ($data['translations'] as $lang => $name) {
      $category->translations()->updateOrCreate(
        ['language' => $lang],
        ['name' => mb_convert_case(trim($name), MB_CASE_TITLE, 'UTF-8')]
      );
    }

    return $category;
  }

  public function delete(Category $category): void
  {
    if ($category->logo && Storage::disk('public')->exists($category->logo)) {
      Storage::disk('public')->delete($category->logo);
    }

    $category->delete();
  }
}
