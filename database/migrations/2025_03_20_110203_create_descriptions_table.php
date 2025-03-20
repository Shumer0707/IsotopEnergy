<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Связь с товаром
            $table->string('language', 2); // 'ru', 'ro', 'en'
            $table->string('title'); // Название товара
            $table->text('short_description'); // Краткое описание
            $table->text('full_description')->nullable(); // Полное описание
            $table->timestamps();

            $table->unique(['product_id', 'language']); // Уникальность описания для каждого языка
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descriptions');
    }
};
