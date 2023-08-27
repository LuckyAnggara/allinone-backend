<?php

namespace App\Http\Controllers;

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
            $sales->save();

            foreach ($data->data as $key => $item) {
                $item = SaleDetail::find($item->id);
            }

            DB::commit();
            return $this->sendResponse($sales, 'Data created', 202);
        }catch (\Exception $e) {

            DB::rollBack();
            return $this->sendResponse($e->getMessage(), 'error', 404);
        }
    }
}
