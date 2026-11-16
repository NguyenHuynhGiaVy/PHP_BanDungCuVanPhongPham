<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }

    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
