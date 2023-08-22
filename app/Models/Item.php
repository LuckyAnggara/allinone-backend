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
        'sku',
        'unit_id',
        'category_id',
        'brand',
        'balance',
        'qty_minimum',
        'iSell',
        'iBuy',
        'selling_price',
        'buying_price',
        'selling_tax_id',
        'buying_tax_id',
        'description',
        'warehouse_id',
        'created_by',
        'archive',
        'branch_id',
    ];

    protected $appends = ['ending_stock', 'in_stock', 'out_stock', 'beg_balance','tax_status','can_tax','tax_value'];

    protected $casts = [
        'iSell' => 'boolean',
        'iBuy' => 'boolean',
    ];

    public function maker()
    {
        return $this->hasOne(Employee::class, 'id', 'created_by')->withTrashed();
    }

    public function unit()
    {
        return $this->hasOne(ItemUnit::class, 'id', 'unit_id')->withTrashed();
    }

    public function category()
    {
        return $this->hasOne(ItemCategory::class, 'id', 'category_id')->withTrashed();
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id')->withTrashed();
    }

    public function buy_tax()
    {
        return $this->hasOne(TaxDetail::class, 'id', 'buying_tax_id')->withTrashed();
    }

    public function sell_tax()
    {
        return $this->hasOne(TaxDetail::class, 'id', 'selling_tax_id')->withTrashed();
    }

    public function mutation()
    {
        return $this->hasMany(ItemMutation::class, 'item_id', 'id')->orderBy('created_at');
    }

    public function price()
    {
        return $this->hasOne(ItemSellingPrice::class, 'item_id', 'id')->ofMany('created_at', 'max');
    }

    public function beginning_balance()
    {
        return $this->hasOne(ItemBeginningStock::class, 'item_id', 'id')->latestOfMany();
    }

    public function getBegBalanceAttribute()
    {
        return [
            'stock' => $this->beginning_balance ?  $this->beginning_balance->stock : 0,
            'price' => $this->beginning_balance ? $this->beginning_balance->price : 0,
            'balance' => ($this->beginning_balance ?  $this->beginning_balance->stock : 0) * ($this->beginning_balance ? $this->beginning_balance->price : 0),
        ];
    }

    public function getInStockAttribute()
    {
        $mutation = $this->hasMany(ItemMutation::class, 'item_id', 'id')->orderBy('created_at');
        return $mutation->sum('debit');
    }

    public function getOutStockAttribute()
    {
        $mutation = $this->hasMany(ItemMutation::class, 'item_id', 'id')->orderBy('created_at');
        return $mutation->sum('credit');
    }

    public function getEndingStockAttribute()
    {
        $mutation = $this->hasMany(ItemMutation::class, 'item_id', 'id')->orderBy('created_at');
        return $this->beginning_balance->stock ?? 0 + $mutation->sum('debit') - $mutation->sum('credit');
    }

    public function getTaxStatusAttribute(){
        return $this->selling_tax_id == 1 ? false : true;
    }

    public function getCanTaxAttribute(){
        return $this->selling_tax_id== 1 ? false : true;
    }
    public function getTaxValueAttribute(){
        return $this->sell_tax->value ?? 0;
    }



          

}
