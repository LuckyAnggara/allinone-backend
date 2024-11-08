<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashTransactionController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->format('Y-m-d');
        $transactions = CashTransaction::whereDate('transaction_date', $today)->get();
        $totalCashIn = $transactions->where('type', 'IN')->sum('amount');
        $totalCashOut = $transactions->where('type', 'OUT')->sum('amount');
        $lastTransaction = CashTransaction::latest()->first();

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
        $user = $data->user;
        try {
            DB::beginTransaction();
            $result = CashTransaction::create($transactions, $user, $data->transactions->description);
            DB::commit();
            return $this->sendResponse($result, 'Data Created', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendResponse($e->getMessage(), 'Error', 404);
        }
    }

    static function create($data, $notes = '')
    {
        return CashTransaction::create([
            'amount' => $data->amount,
            'type' => $data->type,
            'description' => $notes,
            'user_id' => Auth::user()->id,
            'branch_id' => Auth::user()->branch_id,
        ]);
    }
}
