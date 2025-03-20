<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete(); // Категория товара
            $table->string('manufacturer'); // Производитель
            $table->decimal('price', 10, 2); // Цена
            $table->decimal('discount_price', 10, 2)->nullable(); // Цена со скидкой
            $table->string('currency', 3)->default('MDL'); // Валюта (MDL, EUR, USD)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
