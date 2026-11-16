<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_id',
        'supplier_id',
        'order_id',
        'transaction_type',
        'quantity',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function variant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'variant_id'
        );
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
