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
use App\Models\PaymentDetail;
use App\Models\ReturItemSales;
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
                'global_tax' => $data->useGlobalTax,
                'global_tax_id' => $data->tax->id,
                'retur_status' => $data->retur->returStatus,
                'retur_at' => $data->retur->returStatus == false ? null : Carbon::now(),
                'branch_id' => $data->userData->branch_id,
                'created_by' => $data->userData->id,
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
                    'discount' => $value->disc,
                    'tax' => $value->tax
                ]);

                $notes = 'PENJUALAN INVOICE #' . $sales->invoice;
                $link = "/sales/" . $sales->id . "/invoice/";
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

    public function show($uuid)
    {
        $result = Sales::where('uuid', $uuid)
            ->with(['customer', 'detail.item.unit', 'detail.item.sell_tax', 'maker', 'branch', 'payment', 'shipping', 'taxDetail'])
            ->first();
        if ($result->retur_status == 1) {
            $result->append('total_retur')->append('detail_retur');
        }
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent());
        DB::beginTransaction();
        try {
            $sales = Sales::find($id);
            $sales->update([
                'customer_id' => $data->customerData->id,
                'total' => $data->total->subtotal ?? 0,
                'discount' => $data->total->discount ?? 0,
                'tax' => $data->total->tax ?? 0, // pajak
                'shipping_type' => $data->shipping->type ?? 'TAKE AWAY', // TIPE PENGIRIMAN
                'shipping_cost' => $data->shipping->cost ?? 0, //ongkir
                'etc_cost' => $data->total->etc ?? 0, //biaya lainnya
                'etc_cost_desc' => $data->total->etc_desc ?? 0, // keterangan dari biaya lainnya
                'grand_total' => $data->total->grandTotal ?? 0,
                'credit' => $data->credit->isCredit,
                'due_date' => $data->credit->due_date ??  null,
                'payment_type' => $data->transaction->paymentType,
                'payment_status' => $data->transaction->paymentStatus,
                'global_tax' => $data->useGlobalTax,
                'global_tax_id' => $data->tax->id,
                'branch_id' => $data->userData->branch_id,
                'created_by' => $data->userData->id,
            ]);

            if ($data->editCreditPermission == true) {
                if ($sales->credit == 1) {
                    if ($data->credit->isCredit == 0) {
                        $sales->due_date = null;
                        $paymentDetail = PaymentDetail::where('sale_id', $sales->id)->get();
                        foreach ($paymentDetail as $key => $detail) {
                            $detail->delete();
                        }
                    }
                }
            }
            if ($data->shipping->type == 'DELIVERY') {
                ShippingDetailController::create($data->shipping, $sales->id);
            }

            if ($data->editCartPermission == true) {
                $saleDetails = SaleDetail::where('sale_id', $sales->id)->get();
                foreach ($saleDetails as $key => $detail) {
                    $detail->penjualan = false;
                    $notes = 'Ubah Transaksi #' . $sales->invoice;
                    MutationController::create($detail, $data->userData, $notes, '');
                    $detail->delete();
                }
                foreach ($data->currentCart as $value) {
                    SaleDetail::create([
                        'sale_id' => $sales->id,
                        'item_id' => $value->id,
                        'qty' => $value->qty,
                        'price' => $value->price,
                        'discount' => $value->discount,
                        'tax' => $value->tax,
                        'created_at' => $sales->created_at
                    ]);

                    $notes = 'PENJUALAN INVOICE #' . $sales->invoice;
                    $link = '/sales/invoice/' . $sales->id;
                    $itemMutations[] = MutationController::create($value, $data->userData, $notes, $link, $sales->created_at);
                    $itemPrice[] = ItemSellingPriceController::create($value, $sales->created_at);
                }
            }

            if ($data->editReturPermission == true) {
                $returDetail = ReturItemSales::where('sale_id', $sales->id)->get();
                foreach ($returDetail as $key => $detail) {
                    $notes = 'Ubah Retur Product pada Transaksi #' . $sales->invoice;
                    $itemMutations[] = MutationController::create($detail, $data->userData, $notes, '');
                    $detail->delete();
                }
            }

            $sales->save();
            DB::commit();
            return $this->sendResponse($sales, 'Data created', 202);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendResponse($e->getMessage(), 'error', 404);
        }
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
                    $user->branch_id = $sales->branch_id;
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
