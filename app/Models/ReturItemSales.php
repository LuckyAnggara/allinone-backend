<?php

namespace App\Models;

use App\Enums\ReturTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturItemSales extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['sale_id', 'sale_detail_id', 'item_id', 'qty', 'price', 'tax', 'grand_total', 'type', 'notes'];

    protected $casts = [
        'status' => ReturTypeEnum::class,
    ];

    protected $appends = ['item'];

    public function getItemAttribute()
    {
        return Item::where('id', $this->item_id)->first();
    }
}
