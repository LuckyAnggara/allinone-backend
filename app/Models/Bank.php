<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'number_account',
        'name_account',
        'account_id',
        'created_by',
        'branch_id',
    ];

    protected $hidden = [
        'account_id',
            'deleted_at',
        'created_by',
        'created_at',
        'updated_at',
        'branch_id',
    ];
}
