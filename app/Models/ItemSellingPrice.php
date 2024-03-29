<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemSellingPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id',
        'price'
    ];
    protected $casts = [
        'created_at' => 'datetime:d M Y',
    ];


    //  protected $table = 'item_selling_prices';
}
