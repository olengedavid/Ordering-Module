<?php
namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Companies\Warehouse;
use App\Models\Companies\Company;

class DeliveryRegion extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'region',
        'delivery_fee',
        'created_by',
        'status',
        'company_id'
    ];

    protected $casts = [
        'uuid' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model){
            $model->uuid =  Str::uuid();
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}