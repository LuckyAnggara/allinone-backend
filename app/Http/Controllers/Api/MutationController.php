<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseController;
use App\Models\Item;
use App\Models\ItemMutation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutationController extends BaseController
{

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $sku = $request->input('sku');

        $item = Item::where('sku', $sku)->first();

        $result = ItemMutation::where('item_id', $item->id)->latest()
            ->paginate($perPage);
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $customer = ItemMutation::create($request->all());
            DB::commit();
            return $this->sendResponse($customer, 'Data Created', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendResponse($e->getMessage(), 'Error', 404);
        }
    }

    static function create($data, $notes, $link, $date=null)
    {
        $isPenjualan = $data->penjualan ?? true;
        $qty = $data->qty ?? 0;

        $debit = !$isPenjualan ? $qty : 0;
        $credit = !$isPenjualan ? 0 : $qty;

        $notes = $notes ?? 'tidak ada keterangan';
 
        $item = Item::withTrashed()->where('id', $data->id)->first();
        $balance = $isPenjualan ? $item->balance - $qty : $item->balance + $qty;

        try {
             DB::beginTransaction();
             $itemMutation = ItemMutation::create([
            'item_id' => $data->id,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $balance,
            'debit_price' => $data->debit_price ?? 0,
            'kredit_price' => $data->credit_price ?? 0,
            'notes' => $notes,
            'link' => $link,
            'branch_id' =>Auth::user()->branch_id,
            'created_by' => Auth::user()->id,
            'created_at' => $date ?? Carbon::now()
        ]);

        $item->balance = $balance;
        $item->save();

         DB::commit();
        return $itemMutation;

        }catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
       

    }

     static function editCreateFromSales($data, $notes, $link, $date=null)
    {
        $isPenjualan = $data->penjualan ?? true;
        $qty = $data->qty ?? 0;

        $debit = !$isPenjualan ? $qty : 0;
        $credit = !$isPenjualan ? 0 : $qty;

        $notes = $notes ?? 'tidak ada keterangan';
        $branchId = Auth::user()->branch_id;
        $createdBy = Auth::user()->id;


        try {
             DB::beginTransaction();

                     $item = Item::where('id', $data->item_id)->first();
        $balance = $isPenjualan ? $item->balance - $qty : $item->balance + $qty ;

             $itemMutation = ItemMutation::create([
            'item_id' => $data->id,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $balance,
            'debit_price' => $data->debit_price ?? 0,
            'kredit_price' => $data->credit_price ?? 0,
            'notes' => $notes,
            'link' => $link,
            'branch_id' => $branchId,
            'created_by' => $createdBy,
            'created_at' => $date ?? Carbon::now()
        ]);

        $item->balance = $balance;
        $item->save();

         DB::commit();
        return $itemMutation;

        }catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
       

    }
}
