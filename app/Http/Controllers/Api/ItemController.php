<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Mutation;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use Carbon\Carbon;

class ItemController extends BaseController
{

    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);
        $name = $request->input('name');
        $item = Item::with(['brand', 'unit',  'maker']);

        if ($name) {
            $item->where('name', 'like', '%' . $name . '%');
        }

        $result = $item->latest()->paginate($limit);

        return $this->sendResponse($result, 'Data fetched');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $item = Item::create([
            'name' => $request->name,
            'type_id' => $request->type_id,
            'unit_id' => $request->unit_id,
            'warehouse_id' => $request->warehouse_id,
            'created_by' =>  Auth::id(),
        ]);

        if ($item) {
            //membuat saldo awal
            $mutation = new Mutation;
            $mutation->item_id = $item->id;
            $mutation->warehouse_id = $item->warehouse_id;
            $mutation->debit = 0;
            $mutation->kredit = 0;
            $mutation->balance = 0;
            $mutation->notes = 'saldo awal';
            $mutation->created_by = Auth::id();
            $mutation->save();
        }
        return $this->sendResponse((new ItemResource($item)), 'Data created');
    }

    public function show($id)
    {
        $item = Item::with(['type', 'unit', 'warehouse', 'user',])->where('id', $id)->first();
        if (is_null($item)) {
            return $this->sendError('Data does not exist.');
        }
        return $this->sendResponse(new ItemResource($item), 'Data fetched');
    }

    public function update(Request $request, Item $item)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $item->name = $input['name'];
        $item->save();

        return $this->sendResponse(new ItemResource($item), 'Data updated');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        // if ($item) {
        //     Mutation::where('item_id', $item->id)->delete();
        // }
        return $this->sendResponse([], 'Data deleted');
    }
}
