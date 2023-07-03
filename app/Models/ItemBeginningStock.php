<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBeginningStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'stock',
        'price',
        'notes',
    ];
}
