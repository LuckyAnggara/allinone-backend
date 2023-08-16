<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\SaleDetail;
use App\Models\Sales;
use App\Helpers\InvoiceHelper;
use App\Http\Controllers\API\BankTransactionController;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\API\CashTransactionController;
use App\Http\Controllers\API\MutationController;
use App\Http\Controllers\API\ShippingDetailController;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\ItemMutation;
use App\Models\ItemSellingPrice;
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
        $paymentStatus = $request->input('payment-status');
        $deliveryStatus = $request->input('delivery-status');
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
            ->when($paymentStatus, function ($query, $paymentStatus) {
                return $query->where('payment_status', $paymentStatus);
            })
            ->when($deliveryStatus, function ($query, $deliveryStatus) {
                return $query->where('shipping_type', $deliveryStatus);
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
            if (!$data->customerData->withoutCustomer) {
                if (!$customer) {
                    $customer = CustomerController::create($data->customerData, $data->userData);
                }
            }

            $sales = Sales::create([
                'invoice' => InvoiceHelper::generateInvoiceNumber($data->userData->branch_id),
                'customer_id' => $customer->id,
                'total' => $data->total->subtotal ?? 0,
                'discount' => $data->total->discount ?? 0,
                'tax' => $data->total->tax ?? 0, // pajak
                'shipping_type' => $data->shipping->type ?? 'TAKE AWAY', // TIPE PENGIRIMAN
                'shipping_cost' => $data->shipping->fee ?? 0, //ongkir
                'etc_cost' => $data->total->etc ?? 0, //biaya lainnya
                'etc_cost_desc' => $data->total->etc_desc ?? 0, // keterangan dari biaya lainnya
                'grand_total' => $data->total->grandTotal ?? 0,
                'credit' => $data->credit->isCredit,
                'payment_type' => $data->transaction->paymentType,
                'payment_status' => $data->transaction->paymentStatus,
                'branch_id' => $data->userData->branch_id,
                'created_by' => $data->userData->id,
                // 'created_at' => Carbon::today(),
            ]);

            // JIKA KREDIT
            if ($data->credit->isCredit == true) {
                if ($data->credit->amount > 0) {
                    $data->transaction->amount = $data->credit->amount;
                    if ($data->transaction->paymentType == 'CASH') {
                        $transactionNotes = 'Uang masuk Down Payment Invoice - #' . $sales->invoice;
                        CashTransactionController::create($data->transaction, $data->userData, $transactionNotes);
                    } else if ($data->transaction->paymentType == 'TRANSFER') {
                        $transactionNotes = 'Pembayaran ke Bank Down Payment' . $data->transaction->bank->name . ' Invoice - #' . $sales->invoice;
                        BankTransactionController::create($data->transaction, $sales->id, $data->userData, $transactionNotes);
                    }
                }
                PaymentController::create($data->credit, $sales->id);
                $sales->credit = true;
                $carbon = Carbon::createFromFormat('d M Y', $data->credit->due_date);
                // Mengubah format tanggal menjadi "YYYY-MM-DD"
                $formattedDate = $carbon->format('Y-m-d');
                $sales->due_date = $formattedDate;
                $sales->save();
            } else {
                if ($data->transaction->paymentType == 'CASH') {
                    $transactionNotes = 'Uang masuk transaksi Invoice - #' . $sales->invoice;
                    CashTransactionController::create($data->transaction, $data->userData, $transactionNotes);
                } else if ($data->transaction->paymentType == 'TRANSFER') {
                    $transactionNotes = 'Pembayaran ke Bank ' . $data->transaction->bank->name . ' Invoice - #' . $sales->invoice;
                    BankTransactionController::create($data->transaction, $sales->id, $data->userData, $transactionNotes);
                }
            }

            if ($data->shipping->type == 'DELIVERY') {
                ShippingDetailController::create($data->shipping, $sales->id);
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

                $notes = 'PENJUALAN INVOICE #' . $sales->invoice;
                $link = '/sales/invoice/' . $sales->id;
                $itemMutations[] = MutationController::create($value, $data->userData, $notes, $link);
                $itemPrice[] = ItemSellingPriceController::create($value);
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
            ->with(['customer', 'detail.item.unit', 'maker', 'branch', 'payment', 'shipping'])

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
