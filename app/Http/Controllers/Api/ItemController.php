<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;

class ItemController extends BaseController
{

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('name');

        $items = Item::with(['brand', 'unit',  'maker'])
            ->when($name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->latest()
            ->paginate($perPage);

        return $this->sendResponse($items, 'Data fetched');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            // validate request data
            $validatedData = $request->validate([
                'name' => 'required',
                'unit_id' => 'required',
                'brand_id' => 'required',
                // 'warehouse_id' => 'required',
                // 'rack' => 'required',
                'created_by' => 'required',
            ]);
            // create a new instance of YourModel using the validated data
            $item = Item::create($validatedData);
            DB::commit();
            return $this->sendResponse($item, 'Data saved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e, 'Failed to saved data');
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
}
