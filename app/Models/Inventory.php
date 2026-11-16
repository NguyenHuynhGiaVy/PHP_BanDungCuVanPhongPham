<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'variant_id',
        'quantity',
        'reserved_quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved_quantity' => 'integer',
        ];
    }

    public function variant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'variant_id'
        );
    }
}
