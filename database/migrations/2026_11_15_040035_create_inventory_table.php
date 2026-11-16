<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();

            $table->foreignId('variant_id')
                ->unique()
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
