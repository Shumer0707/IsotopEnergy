<?php

namespace App\Services;

use App\Models\Brand;

class BrandService
{
    public function store(array $data): Brand
    {
        return Brand::create([
            'name' => trim($data['name'])
        ]);
    }

    public function update(Brand $brand, array $data): Brand
    {
        $brand->update([
            'name' => trim($data['name'])
        ]);

        return $brand;
    }
}
