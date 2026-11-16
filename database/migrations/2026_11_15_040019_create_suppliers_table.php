<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->string('email', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
