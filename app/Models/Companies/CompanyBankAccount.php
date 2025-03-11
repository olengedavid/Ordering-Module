<?php

namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'company_id',
        'created_by',
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

    public function company()
    {
        return $this -> belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
