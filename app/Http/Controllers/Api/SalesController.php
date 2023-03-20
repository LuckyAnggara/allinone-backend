<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\SaleDetail;
use App\Models\Sales;
use App\Helpers\InvoiceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesController extends BaseController
{
    function store(Request $request)
    {
        $data = json_decode($request->getContent());
        DB::beginTransaction();

        try {
            if (!$data->customer->id) {
                CustomerController::create($data->customer);
            }

            $sales = Sales::create([
                'invoice' => InvoiceHelper::generateInvoiceNumber(),
                'customer_id' => $data->customer->id,
                'total' => $data->total->subTotal ?? 0,
                'discount' => $data->total->discount ?? 0,
                'tax' => $data->total->tax ?? 0, // pajak
                'shipping_cost' => $data->total->shipping ?? 0, //ongkir
                'etc_cost' => $data->total->etc ?? 0, //biaya lainnya
                'etc_cost_desc' => $data->total->etc_desc ?? 0, // keterangan dari biaya lainnya
                'grand_total' => $data->total->total ?? 0,
                'receivable' => 1,
                'branch_id' => 1,
                'created_by' => 1,
                'created_at' => Carbon::today(),
            ]);

            foreach ($data->cart as $value) {
                $saleDetail[] = SaleDetail::create([
                    'sale_id' => $sales->id,
                    'item_id' => $value->id,
                    'qty' => $value->qty,
                    'price' => $value->price,
                ]);
            }
            DB::commit(); // menyimpan perubahan ke database
            return $this->sendResponse($sales, 'Data created', 202);
        } catch (\Exception $e) {
            DB::rollBack(); // membatalkan perubahan

            return $this->sendResponse($e->getMessage(), 'error', 404);
        }
    }
}
