<?php

namespace App\Models\Supplier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SupplierBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'supplier_id',
        'bank_name',
        'account_name',
        'account_number',
        'branch',
        'is_primary'
    ];

    protected $casts = [
        'uuid' => 'string',
        'is_primary' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model){
            $model -> uuid = Str::uuid();
        });
    }

    public function supplier()
    {
        return $this -> belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
