<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\VersionController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ItemBrandController;
use App\Http\Controllers\Api\ItemCategoryController;
use App\Http\Controllers\Api\ItemUnitController;
use App\Http\Controllers\Api\ItemSellingPriceController;
use App\Http\Controllers\API\MutationController;
use App\Http\Controllers\API\ShippingDetailController;
use App\Http\Controllers\Api\TaxController;
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
Route::resource('item-categories', ItemCategoryController::class);
Route::resource('item-units', ItemUnitController::class);
Route::resource('item-mutation', MutationController::class);
Route::resource('item-selling-prices', ItemSellingPriceController::class);
Route::resource('tax-detail', TaxController::class);
Route::resource('shipping-detail', ShippingDetailController::class);
Route::resource('banks', BankController::class);
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
