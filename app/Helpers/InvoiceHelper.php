<?php

namespace App\Helpers;

use App\Models\Branch;
use App\Models\Sales;

class InvoiceHelper
{
    public static function generateInvoiceNumber($branch_id)
    {
        $lastInvoice = Sales::where('branch_id', $branch_id)
            ->orderByDesc('created_at')
            ->first();

        $branch = Branch::find($branch_id);
        $branch_code = $branch->branch_code;

        if (!$lastInvoice) {
            $number = $branch_code . '-1000';
        } else {
            $lastNumber = substr($lastInvoice->invoice, -4);
            $nextNumber = (int) $lastNumber + 1;
            $number = $branch_code . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        // cek apakah nomor invoice sudah ada di database
        while (Sales::where('invoice', $number)->exists()) {
            $lastNumber = substr($number, -4);
            $nextNumber = (int) $lastNumber + 1;
            $number = $branch_code . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return $number;
    }
}
