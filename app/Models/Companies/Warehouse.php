<?php
namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Companies\Company;

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
        'country',
        'region',
        'gps_location',
        'krapin',
        'contact_person',
        'status',
        'company_id'
    ];

    protected $casts = [
        'gps_location' => 'json'
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

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}