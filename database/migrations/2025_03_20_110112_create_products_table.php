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
      $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete(); // Связь с брендом
      $table->string('base_sku')->unique();
      $table->string('measurement')->nullable();
      $table->string('currency', 3)->default('MDL');
      $table->string('main_image')->nullable(); // Главное фото
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};
