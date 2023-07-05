<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_no',
        'name',
        'category',
        'type',
    ];

    public function today()
    {
        return $this->hasOne(Account::class, 'id', 'account_id')->withTrashed();
    }

    public function yesterday()
    {
        return $this->hasOne(Account::class, 'id', 'account_id')->withTrashed();
    }
}
