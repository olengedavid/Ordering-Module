<?php
namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Warehouse extends Model
{
    use HasFactory;

     /**
     * TO be added
     * Country
     * Region
     * Gps Location
     */
    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'email',
        'uuid',
        'created_by',
        'krapin',
        'contact_person',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($warehouse) {
            $warehouse->uuid = Str::uuid()->toString();
        });
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function bankAccounts()
    {
        return $this->hasMany(CompanyBankAccount::class, 'company_id');
    }
}