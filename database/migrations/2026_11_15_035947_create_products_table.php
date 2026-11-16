<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands')
                ->nullOnDelete();

            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);

            $table->timestamps();

            $table->index(['category_id', 'status']);
            $table->index('brand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
