<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'value',
    ];

    protected $hidden = [
        'deleted_at',
        'created_by',
        'created_at',
        'updated_at',
    ];
}
