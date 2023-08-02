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
        'shipping_type',
        'shipping_cost',
        'etc_cost',
        'etc_cost_desc',
        'grand_total',
        'credit',
        'remaining_credit',
        'payment_type',
        'payment_status',
        'due_date',
        'created_by',
        'branch_id',
    ];

    protected $appends = ['remaining_credit', 'total_payment'];

    public function maker()
    {
        return $this->hasOne(Employee::class, 'id', 'created_by')->withTrashed();
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id')->withTrashed();
    }

    public function detail()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id', 'id')->orderBy('created_at');
    }

    public function payment()
    {
        return $this->hasMany(PaymentDetail::class, 'sale_id', 'id')->orderBy('created_at', 'DESC');
    }

    public function getRemainingCreditAttribute()
    {
        return $this->grand_total - $this->payment->sum('payment');
    }

    public function getTotalPaymentAttribute()
    {
        return $this->payment->sum('payment');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id')->withTrashed();
    }
}
