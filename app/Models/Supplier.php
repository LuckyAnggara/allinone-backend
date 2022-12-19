<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'member',
        'company',
        'pic',
        'created_by',
    ];

    public function maker()
    {
        return $this->hasOne(Employee::class, 'id', 'created_by');
    }
}
