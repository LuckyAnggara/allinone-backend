<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\VersionController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ItemBrandController;
use App\Http\Controllers\Api\ItemUnitController;
use App\Http\Controllers\Api\ItemPriceController;
use App\Http\Controllers\API\MutationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Route::resource('items', ItemController::class);
});
Route::resource('version', VersionController::class);
Route::resource('items', ItemController::class);
Route::resource('item-brands', ItemBrandController::class);
Route::resource('item-units', ItemUnitController::class);
Route::resource('item-mutation', MutationController::class);
Route::resource('item-prices', ItemPriceController::class);
Route::resource('customers', CustomerController::class);
Route::resource('sales', SalesController::class);
Route::resource('payment', PaymentController::class);
Route::resource('account', AccountController::class);

Route::get(
    'account-generate',
    [AccountController::class, 'generate']
);

Route::get(
    'price-contoh',
    [ItemPriceController::class, 'contoh']
);

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('logout', 'logout');
});
