<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('language', 2); // 'ru', 'ro', 'en'
            $table->string('name');
            $table->timestamps();

            $table->unique(['category_id', 'language']); // По одной записи на язык
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_translations');
    }
};
