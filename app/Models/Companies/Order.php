<?php

namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\OrderEnums;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use OrderEnums;
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'retailer_id',
        'supplier_id',
        'status',
        'payment_terms',
        'delivery_address',
        'region',
        'expected_delivery_date',
        'delivered_date',
        'delivery_confirmation_code',
        'created_by'
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'delivered_date' => 'date',
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

    public function retailer()
    {
        return $this->belongsTo(Company::class, 'retailer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Company::class, 'supplier_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderedProducts()
    {
        return $this->hasMany(OrderedProduct::class, 'order_id');
    }

    public static function validationRules(): array
    {
        return [
            'retailer_id' => 'required|exists:companies,id',
            'supplier_id' => 'required|exists:companies,id',
            'status' => 'required|in:' . implode(',', self::orderStatuses()),
            'payment_terms' => 'required|in:' . implode(',', self::paymentTerms()),
            'delivery_address' => 'required|string',
            'region' => 'required|string',
            'expected_delivery_date' => 'required|date|after:today',
            'delivered_date' => 'nullable|date',
            'delivery_confirmation_code' => 'nullable|string',
            'created_by' => 'required|exists:users,id',
        ];
    }
}