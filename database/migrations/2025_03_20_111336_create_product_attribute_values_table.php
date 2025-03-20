<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Связь с товаром
            $table->foreignId('attribute_id')->constrained('product_attributes')->onDelete('cascade'); // Связь с атрибутом
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->onDelete('cascade'); // Связь со значением
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};
