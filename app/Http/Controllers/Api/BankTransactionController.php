<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BankTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankTransactionController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->format('Y-m-d');
        $transactions = BankTransaction::whereDate('transaction_date', $today)->get();
        $totalCashIn = $transactions->where('type', 'IN')->sum('amount');
        $totalCashOut = $transactions->where('type', 'OUT')->sum('amount');
        $lastTransaction = BankTransaction::latest()->first();

        $balance = $lastTransaction ? $lastTransaction->balance : 0;
        $currentBalance = $balance + $totalCashIn - $totalCashOut;

        return response()->json([
            'transactions' => $transactions,
            'totalCashIn' => $totalCashIn,
            'totalCashOut' => $totalCashOut,
            'currentBalance' => $currentBalance,
        ]);
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        $transactions = $data->transactions;
        try {
            DB::beginTransaction();
            $result = BankTransaction::create($transactions, $data->transactions->description);
            DB::commit();
            return $this->sendResponse($result, 'Data Created', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendResponse($e->getMessage(), 'Error', 404);
        }
    }

    static function create($data, $saleId, $notes = '')
    {
        return BankTransaction::create([
            'bank_id' => $data->bank->id,
            'sale_id' => $saleId,
            'amount' => $data->amount,
            'type' => $data->type,
            'description' => $notes,
            'user_id' =>  Auth::user()->id,
            'branch_id' =>  Auth::user()->branch_id,
        ]);
    }
}
