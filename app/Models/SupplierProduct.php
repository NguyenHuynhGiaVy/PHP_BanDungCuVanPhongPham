<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'variant_id',
        'import_price',
    ];

    protected function casts(): array
    {
        return [
            'import_price' => 'decimal:2',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function variant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'variant_id'
        );
    }
}
