<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactBalance extends Model
{
    use HasFactory;

    protected $fillable = ['account_id', 'balance'];

    protected $hidden = [
        'id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
        // return $this->hasOne(Account::class, 'id', 'account_id')->withTrashed();
    }
}
