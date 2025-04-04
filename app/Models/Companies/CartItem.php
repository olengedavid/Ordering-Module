<?php

namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Support\Str;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'retailer_id',
        'warehouse_inventory_id',
        'quantity',
        'supplier_id',
        'unit_price',
        'total_price',
        'min_order',
        'max_order',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'min_order' => 'integer',
        'max_order' => 'integer'
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($cartItem) {
            $cartItem->uuid = Str::uuid()->toString();
        });
    }

    public function retailer()
    {
        return $this->belongsTo(Company::class, 'retailer_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'warehouse_inventory_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function supplier()
    {
        return $this->belongsTo(Company::class, 'supplier_id');
    }

    public static function validationRules(): array
    {
        return [
            'retailer_id' => 'required|exists:companies,id',
            'supplier_id' => 'required|exists:companies,id',
            'warehouse_inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'min_order' => 'required|integer|min:0',
            'max_order' => 'required|integer|min:0',
            'created_by' => 'required|exists:users,id'
        ];
    }
}