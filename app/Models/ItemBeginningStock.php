<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItemBeginningStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'stock',
        'price',
        'notes',
    ];

    public static function getTotal()
    {
        return self::select(DB::raw('SUM(stock * price) AS total'))->value('total');
    }
}
