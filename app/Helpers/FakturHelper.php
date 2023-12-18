<?php

namespace App\Helpers;

use App\Models\Branch;
use App\Models\Sales;

class FakturHelper
{
    public static function generateFakturNumber($branch_id)
    {
        $lastFaktur = Sales::where('branch_id', $branch_id)
            ->orderByDesc('created_at')
            ->first();

        $branch = Branch::find($branch_id);
        $branch_code = $branch->branch_code;

        if (!$lastFaktur) {
            $number = $branch_code . '-1000';
        } else {
            $lastNumber = substr($lastFaktur->faktur, -4);
            $nextNumber = (int) $lastNumber + 1;
            $number = $branch_code . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        // cek apakah nomor faktur sudah ada di database
        while (Sales::where('faktur', $number)->exists()) {
            $lastNumber = substr($number, -4);
            $nextNumber = (int) $lastNumber + 1;
            $number = $branch_code . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return $number;
    }
}
