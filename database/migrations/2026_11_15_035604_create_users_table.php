<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')
                ->constrained('roles')
                ->restrictOnDelete();

            $table->string('full_name', 100);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->tinyInteger('status')->default(1);

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
