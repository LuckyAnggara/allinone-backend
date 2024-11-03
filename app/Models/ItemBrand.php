<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemBrand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
       'created_by',
        'branch_id',
    ];

}
