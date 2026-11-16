<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
