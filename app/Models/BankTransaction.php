<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bank_id',
        'sale_id',
        'amount',
        'type',
        'description',
        'user_id',
        'branch_id',
    ];

    const CASH_IN = 'IN';
    const CASH_OUT = 'OUT';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sales::class);
    }


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getTypeAttribute($value)
    {
        return $value == self::CASH_IN ? 'Cash In' : 'Cash Out';
    }
}
