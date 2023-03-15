<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Models\SaleDetail;
use App\Models\Sales;
use App\Models\SalesDetail;
use Illuminate\Support\Facades\DB;

class ItemController extends BaseController
{

    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);
        $name = $request->input('name');
        $item = Item::with(['brand', 'unit',  'maker']);

        if ($name) {
            $item->where('name', 'like', '%' . $name . '%');
        }

        $result = $item->latest()->paginate($limit);

        return $this->sendResponse($result, 'Data fetched');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction(); // memulai transaksi

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
