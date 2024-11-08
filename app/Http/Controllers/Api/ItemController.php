<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\ItemBeginningStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use stdClass;

class ItemController extends BaseController
{

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('name');
        $type = $request->input('type');
        $minSellingPrice = $request->input('min-selling-price');
        $minBuyingPrice = $request->input('min-buying-price');
        $minStock = $request->input('min-stock');
        $category = $request->input('category');

        $user = Auth::user();
        $items = Item::where('branch_id', $user->branch_id)->with(['category', 'unit', 'maker', 'buy_tax', 'sell_tax'])
            ->when($name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%')->orWhere('sku', 'like', '%' . $name . '%');
            })
            ->when($type, function ($query, $type) {
                if ($type == 'sell') {
                    return $query->where('iSell', true);
                } else if ($type == 'buy') {
                    return $query->where('iBuy', true);
                } else {
                    return $query;
                }
            })
            ->when($minSellingPrice, function ($query, $minSellingPrice) {
                return $query->where('selling_price', '>=', $minSellingPrice);
            })
            ->when($minBuyingPrice, function ($query, $minBuyingPrice) {
                return $query->where('buying_price', '>=', $minBuyingPrice);
            })
            ->when($minStock, function ($query, $minStock) {
                return $query->where('balance', '>=', $minStock);
            })
            ->when($category, function ($query, $category) {
                return $query->where('category_id', $category);
            })
            ->latest()
            ->paginate($perPage);

        return $this->sendResponse($items, 'Data fetched');
    }

    public function show($sku)
    {
        $result = Item::where('sku', $sku)
            ->with(['category', 'unit', 'maker',])
            ->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            if ($data->sku == null) {
                $sku = time(); // Contoh: PROD-1637071835
                $data->sku = $sku;
            }
            $item = Item::create([
                'name' => $data->name,
                'sku' =>  $data->sku,
                'unit_id' => $data->unit_id,
                'category_id' => $data->category_id,
                'brand' => $data->brand,
                'balance' => 0,
                'qty_minimum' => $data->qty_minimum,
                'iSell' => $data->iSell,
                'iBuy' => $data->iBuy,
                'selling_price' => $data->selling_price,
                'buying_price' => $data->buying_price,
                'selling_tax_id' => $data->selling_tax_id,
                'buying_tax_id' => $data->buying_tax_id,
                'description' => $data->description,
                'warehouse_id' => $data->warehouse_id,
                'created_by' => Auth::user()->id,
                'branch_id' => Auth::user()->branch_id,
            ]);

            if ($data->beginningStock->value == true) {
                $notes = "Persediaan awal Product " . $item->name;
                $item->stock = $data->beginningStock->stock;
                $item->price = $data->beginningStock->price;
                // ItemBeginningStockController::create($data = $item, $notes = $notes);

                $item->penjualan = false;
                $item->qty = $item->stock;
                $item->debit_price = $item->price;

                MutationController::create($item, $notes, '-');
            }else{
                $item->balance = $data->beginningStock->stock;
                $item->save();
            }
            DB::commit();
            return $this->sendResponse($item, 'Product successfully created');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $item = Item::findOrFail($id);
            $item->update($input);

            DB::commit();
            return $this->sendResponse($item, 'Updated berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Error');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $item = Item::find($id);
            if ($item) {

                $item->delete();
                DB::commit();
                return $this->sendResponse($item, 'Item berhasil dihapus', 200);
            } else {
                return $this->sendError('', 'Data tidak ditemukan', 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
    }

    public function imageUpload(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $item = Item::findOrFail($request->id);
            if ($request->file()) {
                $file_name = time() . '_' . $request->file('file')->getClientOriginalName();

                if ($item) {
                    $file_path = $request->file('file')->store('products', 'public');
                }
                $item->image = $file_path;
                $item->save();
            }
            DB::commit();
            return $this->sendResponse((Storage::url($item->image)), 'Upload Image Berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }

    public function showImage(Request $request)
    {
        $data = json_decode($request->getContent());
        $item = Item::findOrFail(1);

        return (Storage::url($item->image));
    }
}
