<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice',
        'supplier_id',
        'total',
        'discount',
        'tax',
        'shipping_cost',
        'etc_cost',
        'etc_cost_desc',
        'grand_total',
        'debt',
        'remaining_debt',
        'due_date',
        'created_by',
        'branch_id',
    ];
    
    public function maker()
    {
        return $this->hasOne(Employee::class, 'id', 'created_by')->withTrashed();
    }

    public function supplier()
    {
        return $this->hasOne(Employee::class, 'id', 'supplier_id')->withTrashed();
    }

    public function detail()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id', 'id')->orderBy('created_at');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id')->withTrashed();
    }
}
