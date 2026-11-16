<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->restrictOnDelete();

            $table->foreignId('variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->decimal('import_price', 12, 2);

            $table->timestamps();

            $table->unique(['supplier_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
    }
};
