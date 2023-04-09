<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountReceivable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'payment',
        'notes',
        'created_at'
    ];

}
