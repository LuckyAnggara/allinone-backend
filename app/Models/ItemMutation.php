<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemMutation extends Model
{
   use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id',
        'debit',
        'credit',
        'balance',
        'notes',
        'branch_id',
        'created_by',
    ];

    public function maker()
    {
        return $this->hasOne(Employee::class, 'id', 'created_by')->withTrashed();
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id')->withTrashed();
    }


}
