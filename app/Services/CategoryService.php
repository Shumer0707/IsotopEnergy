<?php

namespace App\Services;

use App\Models\Category;

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
        $category = Category::create([
            'parent_id' => $data['parent_id'] ?? null,
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
        $category->update([
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        foreach ($data['translations'] as $lang => $name) {
            $category->translations()->updateOrCreate(
                ['language' => $lang],
                ['name' => mb_convert_case(trim($name), MB_CASE_TITLE, 'UTF-8')]
            );
        }

        return $category;
    }
}
