<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

class VersionController extends BaseController
{

    public function index(Request $request)
    {
        $result = array('version' => '1.0.0');
        return $this->sendResponse($result, 'Data fetched');
    }
}
