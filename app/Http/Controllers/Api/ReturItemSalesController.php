<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Api\BaseController;
use App\Models\ReturItemSales;
use App\Models\Sales;
use App\Models\SaleDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturItemSalesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        DB::beginTransaction();
        try {

            $sales = Sales::where('uuid', $data->uuid)->first();
            $sales->retur = true;
            $sales->retur_at = Carbon::now();

            $total = 0;

            foreach ($data->dataRetur as $key => $item) {
                if ($item->retur_qty > 0) {
                    $detail = SaleDetail::find($item->id);
                    $detail->retur = true;
                    $detail->save();

                    $tax = $item->tax / $item->retur_qty;

                    $retur = ReturItemSales::create([
                        'sale_id' => $sales->id,
                        'sale_detail_id' => $item->id,
                        'item_id' => $item->item_id,
                        'qty' => $item->retur_qty,
                        'price' => $item->price,
                        'tax' => $tax,
                        'grand_total' => ($tax * $item->retur_qty) + ($item->price * $item->retur_qty),
                        'type' => $item->type,
                        'notes' => $item->notes,
                    ]);
                    $total = $total + $retur->grand_total;
                }
            }
            $sales->save();
            DB::commit();
            return $this->sendResponse($total, 'Data created', 202);
        } catch (\Exception $e) {

            DB::rollBack();
            return $this->sendResponse($e->getMessage(), 'error', 404);
        }
    }

    public function show($id)
    {
        $result = ReturItemSales::where('sale_id', $id)->get();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    static function delete($id)
    {
        $result = ReturItemSales::where('id', $id)->first();
        if ($result) {
            $result->delete();
        }
    }
}
