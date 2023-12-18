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
        $sku = $request->input('id');

        $item = Item::where('sku', $sku)->first();
        if ($item) {
            $result = ItemMutation::where('item_id', $item->id)
                ->latest('id')
                ->paginate($perPage);
            if ($result) {
                return $this->sendResponse($result, 'Data fetched');
            }
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

    static function createFromSalesNew($value, $sales)
    {
        $notes = 'penjualan faktur #' . $sales->faktur;
        $link = '/sales/' . $sales->uuid . '/detail/';

        $isDebit = $value->debit ?? true;
        $qty = $value->qty ?? 0;

        $debit = $isDebit ? $qty : 0;
        $credit = !$isDebit ? 0 : $qty;

        $lastBalance =
            ItemMutation::where('id', $value->id)
                ->latest('created_at')
                ->first()->balance ?? 0;
        $balance = $lastBalance + (!$isDebit ? +$qty : -$qty);

        $itemMutation = ItemMutation::create([
            'item_id' => $value->id,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $balance,
            'notes' => $notes,
            'link' => $link,
            'branch_id' =>  Auth::user()->branch_id,
            'created_by' => Auth::id(),
            'created_at' => $date ?? Carbon::now(),
        ]);

        return $itemMutation;
    }

    static function createFromSalesEdit($value, $sales)
    {
        $notes = 'ubah transaksi #' . $sales->faktur;
        $link = '/sales/' . $sales->uuid . '/detail/';

        $isDebit = $value->debit ?? true;
        $qty = $value->qty ?? 0;

        $debit = !$isDebit ? $qty : 0;
        $credit = !$isDebit  ? 0 : $qty;

        $lastBalance =
            ItemMutation::where('id', $value->item_id)
                ->latest('created_at')
                ->first()->balance ?? 0;
        $balance = $lastBalance + (!$isDebit ? +$qty : -$qty);

        $itemMutation = ItemMutation::create([
            'item_id' => $value->item_id,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $balance,
            'notes' => $notes,
            'link' => $link,
            'branch_id' =>  Auth::user()->branch_id,
            'created_by' => Auth::id(),
            'created_at' => $date ?? Carbon::now(),
        ]);

        return $itemMutation;
    }
}
