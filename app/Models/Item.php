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
        'warehouse_id',
        'rack',
        'created_by',
    ];

    protected $appends = ['balance'];

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

    public function getBalanceAttribute()
    {
        return $this->mutation->sum('debit') - $this->mutation->sum('credit');
    }
}
