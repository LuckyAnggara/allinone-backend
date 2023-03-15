<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\SaleDetail;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends BaseController
{
    function store(Request $request)
    {
        try {
            DB::beginTransaction();


            if (($request->customer->id)) {
            
                CustomerController::Store()
            }
            $sales = Sales::create([
                'customer_id' => 1,
                'tanggal_transaksi' => '2023-03-13',
                'total_transaksi' => 100000,
                'status_pembayaran' => 'BELUM LUNAS',
            ]);

            $salesDetail = SaleDetail::create([
                'sales_id' => $sales->id,
                'item_id' => 1,
                'qty' => 2,
                'price' => 50000,
            ]);

            $sales->salesDetails()->save($salesDetail);

            DB::commit(); // menyimpan perubahan ke database
        } catch (\Exception $e) {
            DB::rollBack(); // membatalkan perubahan

            // handle kesalahan
            // contoh:
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}
