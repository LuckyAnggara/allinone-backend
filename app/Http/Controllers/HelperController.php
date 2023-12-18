<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\File;

class HelperController extends BaseController
{
    public function symlink()
    {

        File::link(
            storage_path('app/public'),
            public_path('storage')
        );
        return 'Symlink Success';
    }
}
