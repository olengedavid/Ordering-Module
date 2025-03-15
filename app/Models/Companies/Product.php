<?php
namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
        'sku_number',
        'category',
        'unit_of_measure',
        'description',
        'manufucturer',
        'status',
        'out_of_stock_level',
        'replenish_level',
        'images'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->uuid = Str::uuid()->toString();
        });
    }
}