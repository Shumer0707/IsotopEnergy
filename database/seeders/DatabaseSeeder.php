<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\AdminUserSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductAttributeSeeder::class,
            ProductSeeder::class,
            AttributeValueSeeder::class,
            ProductAttributeValueSeeder::class,
            DescriptionSeeder::class,
        ]);
    }
}
