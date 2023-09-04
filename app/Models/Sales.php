<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

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
        'global_tax',
        'global_tax_id',
        'created_by',
        'retur_at',
        'branch_id',
    ];

    protected $appends = ['remaining_credit', 'total_payment'];
    protected $casts = [
        'iSell' => 'boolean',
        'iBuy' => 'boolean',
        'global_tax' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }

    public function maker()
    {
        return $this->hasOne(Employee::class, 'id', 'created_by')->withTrashed();
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id')->withTrashed();
    }

    public function shipping()
    {
        return  $this->hasOne(ShippingDetail::class, 'sale_id', 'id')->withTrashed();
    }

    public function detail()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id', 'id')->orderBy('created_at');
    }

    public function payment()
    {
        return $this->hasMany(PaymentDetail::class, 'sale_id', 'id')->orderBy('created_at', 'DESC');
    }
    public function taxDetail()
    {
        return $this->hasOne(TaxDetail::class, 'id', 'global_tax_id');
    }

    public function getRemainingCreditAttribute()
    {
        if ($this->credit == true) {
            return $this->grand_total - $this->payment->sum('payment');
        }
        return 0;
    }

    public function getTotalPaymentAttribute()
    {
        if ($this->credit == true) {
            return  $this->payment->sum('payment');
        }
        return  0;
    }

    public function getTotalReturAttribute()
    {
        $retur = $this->hasMany(ReturItemSales::class, 'sale_id', 'id');
        return $retur->sum('grand_total');
    }

    public function getDetailReturAttribute()
    {
        return $this->hasMany(ReturItemSales::class, 'sale_id', 'id')->get();
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id')->withTrashed();
    }
}
