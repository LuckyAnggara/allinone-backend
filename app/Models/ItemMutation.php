<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemMutation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id',
        'debit',
        'credit',
        'debit_price',
        'credit_price',
        'balance',
        'notes',
        'link',
        'branch_id',
        'created_by',
    ];

    public function maker()
    {
        return $this->hasOne(Employee::class, 'id', 'created_by')->withTrashed();
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id')->withTrashed();
    }

    public static function calculateHPP()
    {
        $hpp = self::selectRaw('product_id, SUM(quantity) AS total_quantity, SUM(quantity * price) AS total_cost')
            ->where('transaction_type', 'IN')
            ->groupBy('product_id')
            ->orderBy('transaction_date')
            ->get();

        $totalQuantity = 0;
        $totalCost = 0;
        foreach ($hpp as $item) {
            $item->hpp = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;
            $totalQuantity += $item->total_quantity;
            $totalCost += $item->total_cost;
        }

        return $hpp;
    }
}
