<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::post('auth/login', [AuthController::class, 'login']);
    });

    Route::group(['middleware' => ['auth:admin']], function () {
        Route::get('auth/logout', [AuthController::class, 'logout'])->name('admin.logout');
        Route::apiResource('invoices', InvoiceController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('products', ProductController::class);
    });

});
