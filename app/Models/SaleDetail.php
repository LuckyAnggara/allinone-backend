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
        'discount'
    ];

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id')->withTrashed();
    }

    // public function salesDetails()
    // {
    //     return $this->hasMany(SaleDetail::class);
    // }
}
