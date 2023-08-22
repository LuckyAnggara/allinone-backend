<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'uuid',
        'address',
        'type',
        'phone_number',
        'member',
        'email',
        'company',
        'postalcode',
        'urban',
        'subdistrict',
        'city',
        'pic',
        'created_by',
        'branch_id',
    ];
    protected $casts = [
        'member' => 'boolean',
        'withoutCustomer'=> 'boolean'
    ];

      protected $appends = ['withoutCustomer','existingCustomer'];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }

    public function maker()
    {
        return $this->hasOne(Employee::class, 'id', 'created_by');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function getWithoutCustomerAttribute()
    {
        if($this->id == 1){
            return true;
        }
        return false;
    }

    public function getExistingCustomerAttribute()
    {
        if($this->id !== 1){
            return true;
        }
        return false;
    }
}
