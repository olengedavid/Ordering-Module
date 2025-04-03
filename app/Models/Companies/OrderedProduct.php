<?php

namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderedProduct extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'warehouse_inventory_id',
        'quantity',
        'unit_price',
        'total_price',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            $order->uuid = Str::uuid()->toString();
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'warehouse_inventory_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function validationRules(): array
    {
        return [
            'order_id' => 'required|exists:orders,order_id',
            'warehouse_inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'created_by' => 'required|exists:users,id',
        ];
    }

    public static function createRules(): array
    {
        return [
            'products' => 'required|array|min:1',
            'products.*.warehouse_inventory_id' => 'required|exists:inventories,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ];
    }
}