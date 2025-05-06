<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscountGroup;

class DiscountGroupSeeder extends Seeder
{
    public function run()
    {
        $groups = [
            ['name' => 'Скидка 5%', 'discount_percent' => 5],
            ['name' => 'Скидка 10%', 'discount_percent' => 10],
            ['name' => 'Скидка 15%', 'discount_percent' => 15],
            ['name' => 'Скидка 20%', 'discount_percent' => 20],
        ];

        foreach ($groups as $group) {
            DiscountGroup::create($group);
        }
    }
}
