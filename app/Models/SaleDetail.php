<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'item_id',
        'qty',
        'price',
    ];

    public function item()
    {
        return $this->hasOne(Employee::class, 'id', 'item_id');
    }

    // public function salesDetails()
    // {
    //     return $this->hasMany(SaleDetail::class);
    // }
}
