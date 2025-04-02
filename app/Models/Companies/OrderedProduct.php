<?php

namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class OrderedProduct extends Model
{
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
}