<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attribute_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained()->onDelete('cascade');
            $table->string('language', 2); // 'ru', 'ro', 'en'
            $table->string('name'); // Название атрибута
            $table->timestamps();

            $table->unique(['product_attribute_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_translations');
    }
};
