<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();
            $table->string('discount_type', 20);
            $table->decimal('discount_value', 12, 2);

            $table->decimal('min_order_value', 12, 2)->default(0);
            $table->decimal('max_discount', 12, 2)->nullable();

            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);

            $table->dateTime('start_date');
            $table->dateTime('end_date');

            $table->tinyInteger('status')->default(1);

            $table->timestamps();

            $table->index(['status', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
