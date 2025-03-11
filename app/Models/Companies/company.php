<?php
namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;
    protected $fillable =[
        'company_name',
        'office_address',
        'email',
        'phone_number',
        'krapin',
        'industry',
        'status',
        'contact_person',
        'created_by'
    ];

    protected $casts = [
        'uuid' => 'string',
    ];

    protected $attributes = [
        'status' => 'string',
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

    public function bankAccounts()
    {
        return $this->hasMany(CompanyBankAccount::class, 'company_id');
    }
    
}