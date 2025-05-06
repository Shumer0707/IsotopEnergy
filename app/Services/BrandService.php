<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

class BrandService
{
  public function store(array $data): Brand
  {
    $logoPath = null;

    if (isset($data['logo'])) {
      $logoPath = $data['logo']->store('brands', 'public');
    }

    return Brand::create([
      'name' => trim($data['name']),
      'logo' => $logoPath
    ]);
  }

  public function update(Brand $brand, array $data): Brand
  {
    if (isset($data['logo'])) {
      if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
        Storage::disk('public')->delete($brand->logo);
      }
      $data['logo'] = $data['logo']->store('brands', 'public');
    } else {
      unset($data['logo']); // чтобы не затереть старый путь
    }

    $brand->update([
      'name' => trim($data['name']),
      'logo' => $data['logo'] ?? $brand->logo,
    ]);

    return $brand;
  }

  public function delete(Brand $brand): void
  {
    if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
      Storage::disk('public')->delete($brand->logo);
    }

    $brand->delete();
  }
}
