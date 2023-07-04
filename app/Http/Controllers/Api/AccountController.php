<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Api\BaseController;
use App\Models\Account;
use Illuminate\Support\Facades\DB;


class AccountController extends BaseController
{
    public function index(Request $request)
    {
        $result = Account::all();

        return $this->sendResponse($result, 'Data fetched');
    }
}
