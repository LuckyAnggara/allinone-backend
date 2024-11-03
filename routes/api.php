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
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\API\ShippingDetailController;
use App\Http\Controllers\Api\TaxController;
use App\Http\Controllers\Api\ReturItemSalesController;
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
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::resource('item-mutation', MutationController::class);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user'])->name('user');
    Route::resource('items', ItemController::class);
    Route::resource('item-brands', ItemBrandController::class);
    Route::resource('item-categories', ItemCategoryController::class);
    Route::resource('item-units', ItemUnitController::class);
    Route::resource('item-selling-prices', ItemSellingPriceController::class);
    Route::resource('sales', SalesController::class);
Route::resource('banks', BankController::class);

});

Route::resource('version', VersionController::class);
Route::resource('tax-detail', TaxController::class);
Route::resource('shipping-detail', ShippingDetailController::class);
Route::resource('customers', CustomerController::class);
Route::resource('sales-retur', ReturItemSalesController::class);
Route::resource('sales-credit-payment', PaymentController::class);
Route::resource('notification', NotificationController::class);
Route::resource('account', AccountController::class);

Route::get(
    'account-generate',
    [AccountController::class, 'generate']
);

Route::get(
    'price-contoh',
    [ItemPriceController::class, 'contoh']
);


Route::post(
    'item/upload-image',
    [ItemController::class, 'imageUpload']
);

Route::get(
    'item/show-image',
    [ItemController::class, 'showImage']
);

Route::get(
    '/notification/get-unread/{id}',
    [NotificationController::class, 'getUnread']
);

