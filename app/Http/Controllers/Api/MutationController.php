<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseController;
use App\Models\ItemMutation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutationController extends BaseController
{

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $id = $request->input('id');

        $result = ItemMutation::where('item_id', $id)->latest()
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



    static function create($data, $user, $notes, $link, $date=null)
    {
        $isPenjualan = $data->penjualan ?? true;
        $qty = $data->qty ?? 0;

        $debit = !$isPenjualan ? $qty : 0;
        $credit = !$isPenjualan ? 0 : $qty;

        $notes = $notes ?? 'tidak ada keterangan';
        $branchId = $user->branch_id;
        $createdBy = $user->id;

        $itemMutation = ItemMutation::create([
            'item_id' => $data->id,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => 0,
            'notes' => $notes,
            'link' => $link,
            'branch_id' => $branchId,
            'created_by' => $createdBy,
            'created_at' => $date ?? Carbon::now()
        ]);

        return $itemMutation;
    }
}
