<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'unit_id',
        'brand_id',
        'stock',
        'beginning_stock',
        'warehouse_id',
        'rack',
        'created_by',
    ];

    protected $appends = ['ending_stock','in_stock','out_stock','beginning_balance'];

    public function maker()
    {
        return $this->hasOne(Employee::class, 'id', 'created_by')->withTrashed();
    }

    public function unit()
    {
        return $this->hasOne(ItemUnit::class, 'id', 'unit_id')->withTrashed();
    }

    public function brand()
    {
        return $this->hasOne(ItemBrand::class, 'id', 'brand_id')->withTrashed();
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id')->withTrashed();
    }

    public function mutation()
    {
        return $this->hasMany(ItemMutation::class, 'item_id', 'id')->orderBy('created_at');
    }

    public function buying_price()
    {
       return $this->hasOne(ItemBuyingPrice::class, 'item_id', 'id')->ofMany('created_at', 'max');
    }

    public function price()
    {
       return $this->hasOne(ItemPrice::class, 'item_id', 'id')->ofMany('created_at', 'max');
    }

    public function getInStockAttribute()
    {
        return $this->mutation->sum('debit') ;
    }

    public function getOutStockAttribute()
    {
        return $this->mutation->sum('credit');
    }

    public function getEndingStockAttribute()
    {
        return $this->beginning_stock - $this->mutation->sum('debit') - $this->mutation->sum('credit');
    }

    public function getBeginningBalanceAttribute()
    {
        return $this->beginning_stock * $this->buying_price->price;
    }

}
