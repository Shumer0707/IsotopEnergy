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
            'categories' => $query->get(),
            'parentCategories' => Category::whereNull('parent_id')->get(),
            'filters' => [
                'filter' => $filter,
                'parent_id' => $parentId,
            ],
        ];
    }

    public function store(array $data): Category
    {
        return Category::create([
            'name_ru' => mb_convert_case(trim($data['name_ru']), MB_CASE_TITLE, 'UTF-8'),
            'name_ro' => mb_convert_case(trim($data['name_ro']), MB_CASE_TITLE, 'UTF-8'),
            'name_en' => mb_convert_case(trim($data['name_en']), MB_CASE_TITLE, 'UTF-8'),
            'parent_id' => $data['parent_id'] ?? null,
        ]);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update([
            'name_ru' => mb_convert_case(trim($data['name_ru']), MB_CASE_TITLE, 'UTF-8'),
            'name_ro' => mb_convert_case(trim($data['name_ro']), MB_CASE_TITLE, 'UTF-8'),
            'name_en' => mb_convert_case(trim($data['name_en']), MB_CASE_TITLE, 'UTF-8'),
        ]);

        return $category;
    }
}
