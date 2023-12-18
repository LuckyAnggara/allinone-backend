<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Api\BaseController;
use App\Models\Account;
use App\Models\FactBalance;
use App\Models\ItemBeginningStock;
use App\Models\Sales;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AccountController extends BaseController
{
    public function index(Request $request)
    {
        $result = Account::with('factBalance')->where('category', 'LABA_RUGI')->get();
        $today = Carbon::now();

        return $this->sendResponse($result, 'Data fetched');
    }

    public function generate()
    {

 
        
        $today = Carbon::now();
        $yesterday = Carbon::yesterday();
        
        DB::beginTransaction();
        try {
            $data = [];
            $result = FactBalance::whereDate('created_at', $today)->get();

            if ($result) {
                FactBalance::whereDate('created_at', $today)->delete();
            }

            $penjualan = Sales::whereDate('created_at', $today)->sum('total'); //1
            $retur_penjualan = 0; // 2
            $diskon = Sales::whereDate('created_at', $today)->sum('discount'); //3
            $penjualan_kotor = $penjualan - $retur_penjualan - $diskon; //4
            $persediaan_awal = ItemBeginningStock::getTotal();


            //Input Penjualan
            FactBalance::create([
                'account_id' => 1,
                'balance' => $penjualan,
            ]);
            //Input Retur Penjualan
            FactBalance::create([
                'account_id' => 2,
                'balance' => $retur_penjualan,
            ]);
            //Input Diskon
            FactBalance::create([
                'account_id' => 3,
                'balance' => $diskon,
            ]);
            //Input Penjualan Kotor
            FactBalance::create([
                'account_id' => 4,
                'balance' => $penjualan_kotor,
            ]);
            //Persediaan Awal
            FactBalance::create([
                'account_id' => 5,
                'balance' => $persediaan_awal,
            ]);

            DB::commit();

            $account = Account::where('category', 'LABA_RUGI')->get();

            foreach ($account as $key => $value) {
                $value->today =  FactBalance::where('account_id', $value->id)->whereDate('created_at', $today)->first();
                $value->yesterday =  FactBalance::where('account_id', $value->id)->whereDate('created_at', $yesterday)->first();
            }

            return $this->sendResponse($account, 'Data created', 202);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendResponse($e->getMessage(), 'error', 404);
        }
    }
}
