<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\SaleDetail;
use App\Models\Sales;
use App\Helpers\InvoiceHelper;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\API\CashTransactionController;
use App\Http\Controllers\API\MutationController;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\ItemMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use stdClass;

class SalesController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('name');
        $branch = $request->input('branch');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');
        $minTotal = $request->input('min-total');
        $status = $request->input('status');
        $credit = $request->input('credit');

        // $result = Sales::where('credit', $credit)->get();
        // return $result;


        $result = Sales::select('sales.*')
            ->join('customers', 'customers.id', '=', 'sales.customer_id')
            ->when($name, function ($query, $name) {
                return $query
                    ->where('customers.name', 'like', '%' . $name . '%')
                    ->orWhere('invoice', 'like', '%' . $name . '%')
                    ->orWhere('grand_total', 'like', '%' . $name . '%');
            })
            ->when($branch, function ($query, $branch) {
                return $query->where('sales.branch_id', $branch);
            })
            ->when($minTotal, function ($query, $minTotal) {
                return $query->where('grand_total', '>=', $minTotal);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($credit, function ($query, $credit) {
                return $query->where('credit', $credit);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d M Y', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('d M Y', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->with(['customer',  'maker', 'branch'])
            ->orderBy('sales.created_at', 'desc')
            ->paginate($perPage);

        return $this->sendResponse($result, 'Data fetched');
    }

    function store(Request $request)
    {
        $data = json_decode($request->getContent());
        DB::beginTransaction();
        try {
            $customer = Customer::find($data->customerData->id);
            if (!$customer) {
                $customer = CustomerController::create($data->customerData, $data->user);
            }
            $sales = Sales::create([
                'invoice' => InvoiceHelper::generateInvoiceNumber($data->user->branchId),
                'customer_id' => $customer->id,
                'total' => $data->total->subTotal ?? 0,
                'discount' => $data->total->discount ?? 0,
                'tax' => $data->total->tax ?? 0, // pajak
                'shipping_cost' => $data->total->shipping ?? 0, //ongkir
                'etc_cost' => $data->total->etc ?? 0, //biaya lainnya
                'etc_cost_desc' => $data->total->etc_desc ?? 0, // keterangan dari biaya lainnya
                'grand_total' => $data->total->total ?? 0,
                'credit' => $data->transaction->isCredit,
                'status' => $data->transaction->isCredit ? 'BELUM LUNAS' : 'LUNAS',
                'branch_id' => $data->user->branchId,
                'created_by' => $data->user->id,
                'created_at' => Carbon::today(),
            ]);
            if ($data->transaction->isCash == true) {
                $transactionNotes = 'UANG MASUK DARI TRANSAKSI INVOICE - #' . $sales->invoice;
                CashTransactionController::create($data->transaction, $data->user, $transactionNotes);
            }

            if ($data->transaction->isCredit == true) {
                if ($data->credit->amount > 0) {
                    $transactionNotes = 'DOWN PAYEMENT UNTUK PIUTANG INVOICE - #' . $sales->invoice;
                    $data->transaction->amount = $data->credit->amount;
                    CashTransactionController::create($data->transaction, $data->user, $transactionNotes);
                }
                PaymentController::create($data->credit, $sales->id);
                $sales->credit = true;
                $carbon = Carbon::createFromFormat('d M Y', $data->credit->due_date);
                // Mengubah format tanggal menjadi "YYYY-MM-DD"
                $formattedDate = $carbon->format('Y-m-d');
                $sales->due_date = $formattedDate;
                $sales->save();
            }

            $saleDetails = [];
            $itemMutations = [];

            foreach ($data->currentCart as $value) {
                $saleDetails[] = SaleDetail::create([
                    'sale_id' => $sales->id,
                    'item_id' => $value->id,
                    'qty' => $value->qty,
                    'price' => $value->price,
                    'discount' => $value->disc
                ]);

                $notes = 'PENJUALAN TRANSAKSI INVOICE #' . $sales->invoice;
                $link = '/sales/invoice/' . $sales->id;
                $itemMutations[] = MutationController::create($value, $data->user, $notes, $link);
                $itemPrice[] = ItemPriceController::create($value);
            }

            DB::commit();
            return $this->sendResponse($sales, 'Data created', 202);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendResponse($e->getMessage(), 'error', 404);
        }
    }

    public function show($id)
    {
        $result = Sales::where('id', $id)
            ->with(['customer', 'detail.item.unit', 'maker', 'branch', 'payment'])

            ->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $sales = Sales::find($id);
            if ($sales) {
                $saleDetails = SaleDetail::where('sale_id', $sales->id)->get();
                foreach ($saleDetails as $key => $detail) {
                    $detail->id = $detail->item_id;
                    $detail->penjualan = false;
                    $user = new stdClass();
                    $user->branchId = $sales->branch_id;
                    $user->id = $sales->created_by;
                    $notes = 'Hapus Transaksi #' . $sales->invoice;
                    MutationController::create($detail, $user, $notes, '');
                    // $detail->delete();
                }
                $sales->delete();
                DB::commit();
                return $this->sendResponse($saleDetails, 'Data sales berhasil dihapus', 200);
            } else {
                return $this->sendError('', 'Data sales tidak ditemukan', 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
    }
}
