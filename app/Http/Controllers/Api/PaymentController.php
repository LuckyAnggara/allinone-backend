<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Api\BaseController;
use App\Models\PaymentDetail;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PaymentController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($sale_id)
    {
        $accountReceivables = PaymentDetail::where('sale_id', $sale_id)->get();
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
                'amount' => 'required|numeric',
                'notes' => 'nullable|string',
                'created_at' => 'nullable|date',
            ]);

            $accountReceivable = PaymentDetail::create($validated);

            $sales = Sales::find($request->sale_id);
            if ($sales->remaining_credit <= 0) {
                $sales->payment_status = 'LUNAS';
                $sales->save();

            }

            DB::commit();
            return $this->sendResponse($accountReceivable, 'Data created', 201);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 'Failed to store account receivable', 500);
        }
    }

    static function create($data, $id)
    {
        PaymentDetail::create([
            'sale_id' => $id,
            'notes' => $data->notes,
            'amount' => $data->amount,
        ]);
    }

    public function show($id)
    {
        $accountReceivable = PaymentDetail::findOrFail($id);
        return $this->sendResponse($accountReceivable, 'Data fetched');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sale_id' => 'required',
            'amount' => 'required|numeric',
            'note' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $accountReceivable = PaymentDetail::findOrFail($id);
        $accountReceivable->update($validated);

        return $this->sendResponse($accountReceivable, 'Data fetched');
    }

    public function destroy($id)
    {
        $paymentDetail = PaymentDetail::findOrFail($id);
        $paymentDetail->delete();

        $dataSales = Sales::find($paymentDetail->sale_id);
        if ($dataSales->remaining_credit > 0) {
            $dataSales->payment_status = 'BELUM LUNAS';
            $dataSales->save();
        }
        return $this->sendResponse($paymentDetail, 'Data fetched');
    }
}
