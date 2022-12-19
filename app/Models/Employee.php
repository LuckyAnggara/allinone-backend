<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'join_date',
        'leave_date',
        'role_id',
        'picture',
        'is_active',
    ];

    // public function role()
    // {
    //     return $this->hasOne(Role::class, 'id', 'role_id');
    // }
}
