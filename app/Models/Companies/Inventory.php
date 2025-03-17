<?php

namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Companies\Company;
// use App\Models\Companies\Product;
// use App\Models\Companies\Warehouse;

class Inventory extends Model
{
    //

    protected $fillable =[
        'created_by',
        'company_id',
        'product_id',
        'warehouse_id',
        'cost_price',
        'stock_quantity',
        'selling_price',
        'min_order',
        'max_order',
        'promo_amount',
        'promo_start_date',
        'promo_end_date',
        'status'
    ];

    protected $casts = [
        'uuid' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($inventory) {
            $inventory->uuid = Str::uuid()->toString();
        });
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
