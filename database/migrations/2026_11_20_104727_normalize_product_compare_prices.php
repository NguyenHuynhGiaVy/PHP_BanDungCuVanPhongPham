<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('product_variants')
            ->where(function (Builder $query): void {
                $query->whereNull('compare_price')
                    ->orWhereColumn('compare_price', '<=', 'price');
            })
            ->update(['compare_price' => null]);

        // If data was seeded with a 10% artificial difference, remove it too
        // unless it represents a deliberate real discount.
        DB::table('product_variants')
            ->whereRaw('compare_price IS NOT NULL')
            ->whereRaw('ABS(compare_price - price * 1.1) < 0.01')
            ->update(['compare_price' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
