<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'bank_id',
        'payment_status',
        'amount',
        'notes',
        'created_at'
    ];

}
