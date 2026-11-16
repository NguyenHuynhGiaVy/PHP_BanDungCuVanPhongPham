<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'variant_name',
        'attributes',
        'price',
        'compare_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'status' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'variant_id');
    }

    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class, 'variant_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'variant_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(
            InventoryTransaction::class,
            'variant_id'
        );
    }
}
