<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Создание таблиц для вариантов товаров
   */
  public function up(): void
  {
    // Таблица вариантов товаров
    Schema::create('product_variants', function (Blueprint $table) {
      $table->id();
      $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Связь с основным товаром
      $table->string('sku')->unique(); // Уникальный артикул варианта (автогенерируемый)
      $table->decimal('price', 10, 2); // Цена конкретного варианта
      $table->boolean('is_default')->default(false); // Является ли вариант основным для отображения
      $table->timestamps();

      // Индексы для быстрого поиска
      $table->index('product_id');
      $table->index('is_default');
    });

    // Связующая таблица: какие атрибуты формируют конкретный вариант
    Schema::create('product_variant_attributes', function (Blueprint $table) {
      $table->id();
      $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade'); // Вариант товара
      $table->foreignId('attribute_id')->constrained('product_attributes')->onDelete('cascade'); // Атрибут (толщина, плотность)
      $table->foreignId('attribute_value_id')->constrained('attribute_values')->onDelete('cascade'); // Значение атрибута (50мм, 15кг/м³)
      $table->timestamps();

      // Уникальный индекс: один атрибут = одно значение в рамках варианта
      $table->unique(['variant_id', 'attribute_id'], 'variant_attribute_unique');

      // Индексы для быстрого поиска
      $table->index('variant_id');
      $table->index(['attribute_id', 'attribute_value_id']);
    });
  }

  /**
   * Откат миграции
   */
  public function down(): void
  {
    Schema::dropIfExists('product_variant_attributes');
    Schema::dropIfExists('product_variants');
  }
};
