<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Api\BaseController;
use App\Models\AccountReceivable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AccountReceivableController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($sale_id)
    {
        $accountReceivables = AccountReceivable::where('sale_id', $sale_id)->get();
        return $this->sendResponse($accountReceivables, 'Data fetched');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = collect($request->all());
            // Mengubah kunci camelCase menjadi snake_case pada collection data
            $data = $data->mapWithKeys(function ($value, $key) {
                return [Str::snake($key) => $value];
            });

            $validated = $request->validate([
                'sale_id' => 'required',
                'payment' => 'required|numeric',
                'note' => 'nullable|string',
                'created_at' => 'nullable|date',
            ]);

            $accountReceivable = AccountReceivable::create($validated);
            DB::commit();

            return $this->sendResponse($accountReceivable, 'Data created', 201);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 'Failed to store account receivable', 500);
        }
    }

    static function create($data, $id)
    {
        AccountReceivable::create([
            'sale_id' => $id,
            'notes' => $data->notes,
            'payment'=> $data->amount
        ]);
    }


    public function show($id)
    {
        $accountReceivable = AccountReceivable::findOrFail($id);
        return $this->sendResponse($accountReceivable, 'Data fetched');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sale_id' => 'required',
            'payment' => 'required|numeric',
            'note' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $accountReceivable = AccountReceivable::findOrFail($id);
        $accountReceivable->update($validated);

        return $this->sendResponse($accountReceivable, 'Data fetched');
    }

    public function destroy($id)
    {
        $accountReceivable = AccountReceivable::findOrFail($id);
        $accountReceivable->delete();

        return $this->sendResponse($accountReceivable, 'Data fetched');
    }
}
