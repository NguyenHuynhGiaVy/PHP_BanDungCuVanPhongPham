<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_value',
        'max_discount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_order_value' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'status' => 'integer',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
