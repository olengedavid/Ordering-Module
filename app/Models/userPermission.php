<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Companies\Company;
use App\Models\Companies\Warehouse;

class userPermission extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'permissions',
        'warehouse_id',
        'created_by',
        'updated_by',
        'entity_type',
    ];

    protected $casts = [
        'uuid' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->uuid = Str::uuid()->toString();
        });
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    
}
