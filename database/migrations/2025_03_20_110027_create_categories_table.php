<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('categories', function (Blueprint $table) {
      $table->id();
      $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
      $table->string('logo')->nullable(); // 🔹 добавили
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('categories');
  }
};
