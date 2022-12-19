<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
        use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice',
        'customer_id',
        'total',
        'discount',
        'tax',
        'shipping_cost',
        'etc_cost',
        'etc_cost_desc',
        'grand_total',
        'receivable',
        'remaining_receivable',
        'due_date',
        'created_by',
        'branch_id',
    ];
    
    public function maker()
    {
        return $this->hasOne(Employee::class, 'id', 'created_by');
    }

    public function customer()
    {
        return $this->hasOne(Employee::class, 'id', 'customer_id');
    }

    public function detail()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id', 'id')->orderBy('created_at');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }
}
