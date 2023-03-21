<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\SaleDetail;
use App\Models\Sales;
use App\Helpers\InvoiceHelper;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('name');
        $branch = $request->input('branch');
        $startDate = $request->input('branch');
        $endDate = $request->input('endDate');
        $invoice = $request->input('invoice');
        $minTotal = $request->input('mintotal');

        $result = Sales::with(['customer', 'detail', 'maker', 'branch'])
            ->when($name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($branch, function ($query, $branch) {
                return $query->where('branch_id', $branch);
            })
            ->when($invoice, function ($query, $invoice) {
                return $query->where('invoice', 'like', '%' . $invoice . '%');
            })
            ->when($minTotal, function ($query, $minTotal) {
                return $query->where('grand_total', '>=', $minTotal);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->sendResponse($result, 'Data fetched');
    }

    function store(Request $request)
    {
        $data = json_decode($request->getContent());
        DB::beginTransaction();

        try {
            $customer = Customer::find($data->customer->id);
            if (!$customer) {
                $customer = CustomerController::create($data->customer);
            }

            $sales = Sales::create([
                'invoice' => InvoiceHelper::generateInvoiceNumber(),
                'customer_id' => $customer->id,
                'total' => $data->total->subTotal ?? 0,
                'discount' => $data->total->discount ?? 0,
                'tax' => $data->total->tax ?? 0, // pajak
                'shipping_cost' => $data->total->shipping ?? 0, //ongkir
                'etc_cost' => $data->total->etc ?? 0, //biaya lainnya
                'etc_cost_desc' => $data->total->etc_desc ?? 0, // keterangan dari biaya lainnya
                'grand_total' => $data->total->total ?? 0,
                'receivable' => 1,
                'branch_id' => 1,
                'created_by' => $data->userId,
                'created_at' => Carbon::today(),
            ]);

            foreach ($data->cart as $value) {
                $saleDetail[] = SaleDetail::create([
                    'sale_id' => $sales->id,
                    'item_id' => $value->id,
                    'qty' => $value->qty,
                    'price' => $value->price,
                ]);
            }
            DB::commit();
            return $this->sendResponse($sales, 'Data created', 202);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendResponse($e->getMessage(), 'error', 404);
        }
    }

    // function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'customer' => 'required',
    //         'cart' => 'required|array',
    //         'total' => 'required',
    //         'userId' => 'required'
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         $customer = Customer::firstOrCreate(['id' => $data['customer']['id']], $data['customer']);

    //         $sales = Sales::create([
    //             'invoice' => InvoiceHelper::generateInvoiceNumber(),
    //             'customer_id' => $customer->id,
    //             'total' => $data['total']['subTotal'] ?? 0,
    //             'discount' => $data['total']['discount'] ?? 0,
    //             'tax' => $data['total']['tax'] ?? 0,
    //             'shipping_cost' => $data['total']['shipping'] ?? 0,
    //             'etc_cost' => $data['total']['etc'] ?? 0,
    //             'etc_cost_desc' => $data['total']['etc_desc'] ?? '',
    //             'grand_total' => $data['total']['total'] ?? 0,
    //             'receivable' => 1,
    //             'branch_id' => 1,
    //             'created_by' => $data['userId'],
    //             'created_at' => Carbon::today(),
    //         ]);

    //         $saleDetails = collect($data['cart'])->map(function ($item) use ($sales) {
    //             return new SaleDetail([
    //                 'item_id' => $item['id'],
    //                 'qty' => $item['qty'],
    //                 'price' => $item['price'],
    //             ]);
    //         });

    //         $sales->salesDetails()->saveMany($saleDetails);
    //         DB::commit();
    //         return $this->sendResponse($sales, 'Data created', 202);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return $this->sendResponse($e->getMessage(), 'error', 404);
    //     }
    // }
}
