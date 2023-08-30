<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Api\BaseController;
use App\Models\ReturItemSales;
use App\Models\Sales;
use App\Models\SaleDetail;
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

            $retur = [];

            foreach ($data->dataRetur as $key => $item) {
                if($item->retur_qty > 0){
                  $detail = SaleDetail::find($item->id);
                  $detail->retur = true;
                  $detail->save();

                 $retur[]= ReturItemSales::create([
                        'sale_id' => $sales->id,
                        'sale_detail_id' => $item->id,
                        'item_id' => $item->item_id,
                        'qty' => $item->retur_qty,
                        'price' => $item->price,
                        'type' => $item->type,
                        'notes' => $item->notes,
                  ]);
                }
            }
            $sales->save();
            DB::commit();
            return $this->sendResponse($retur, 'Data created', 202);
        }catch (\Exception $e) {

            DB::rollBack();
            return $this->sendResponse($e->getMessage(), 'error', 404);
        }
    }
}
