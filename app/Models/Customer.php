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
        // 'type',
        'address',
        'phone_number',
        'member',
        'company',
        'pic',
        'created_by',
        'branch_id',
    ];
    protected $casts = [
        'member' => 'boolean',

    ];

    protected $primaryKey = 'member_number';
    public $incrementing = false;


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
}
