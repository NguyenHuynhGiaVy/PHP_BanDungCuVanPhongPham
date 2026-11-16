<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('suppliers')
                ->nullOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            $table->string('transaction_type', 30);
            $table->unsignedInteger('quantity');
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['variant_id', 'transaction_type']);
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
