<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getTypeAttribute($value)
    {
        return $value == self::CASH_IN ? 'Cash In' : 'Cash Out';
    }
}
