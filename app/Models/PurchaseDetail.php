<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_id',
        'item_id',
        'qty',
        'price',
    ];

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id')->withTrashed();
    }
}
