<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\Admin\EmployeesController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ActivityLogController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/test_mail', [\App\Http\Controllers\HomeController::class, 'testMail']);

Route::middleware('guest')->group(function () {
// Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

//Language Translation
Route::get('index/{locale}', [HomeController::class, 'lang']);

Route::redirect('/', '/admin');

Route::prefix('/admin')->middleware('auth')->group(function () {

    Route::get('/', [HomeController::class, 'root'])->name('root');
    Route::get('/home/orders/data', [HomeController::class, 'newOrdersData'])->name('home.orders.data');

    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::resource('/customers', CustomerController::class)->names('customers');
        Route::get('/customers/get/data', [CustomerController::class, 'getData'])->name('customers.data');

    });

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('/roles', RolesController::class)->names('roles')->except('show');
    Route::get('/roles/data', [RolesController::class, 'getData'])->name('roles.data');
    Route::post('/roles/bulkDelete', [RolesController::class, 'bulkDelete'])->name('roles.bulkDelete');
    Route::post('/roles/changeStatus', [RolesController::class, 'changeStatus'])->name('roles.changeStatus');

    Route::resource('/admins', AdminsController::class)->names('admins')->except('show');
    Route::get('/admins/data', [AdminsController::class, 'getData'])->name('admins.data');
    Route::post('/admins/bulkDelete', [AdminsController::class, 'bulkDelete'])->name('admins.bulkDelete');
    Route::post('/admins/changeStatus', [AdminsController::class, 'changeStatus'])->name('admins.changeStatus');

    Route::resource('/employees', EmployeesController::class)->names('employees')->except('show');
    Route::get('/employees/data', [EmployeesController::class, 'getData'])->name('employees.data');
    Route::post('/employees/bulkDelete', [EmployeesController::class, 'bulkDelete'])->name('employees.bulkDelete');
    Route::post('/employees/changeStatus', [EmployeesController::class, 'changeStatus'])->name('employees.changeStatus');

    Route::resource('/products', ProductsController::class)->names('products')->except('show');
    Route::get('/products/data', [ProductsController::class, 'getData'])->name('products.data');
    Route::post('/products/bulkDelete', [ProductsController::class, 'bulkDelete'])->name('products.bulkDelete');
    Route::post('/products/changeStatus', [ProductsController::class, 'changeStatus'])->name('products.changeStatus');


    Route::resource('/invoices', InvoiceController::class)->names('invoices');
    Route::get('/invoices/get/data', [InvoiceController::class, 'getData'])->name('invoices.data');
    Route::post('/invoices/bulkDelete', [InvoiceController::class, 'bulkDelete'])->name('invoices.bulkDelete');
    Route::post('/invoices/changeStatus', [InvoiceController::class, 'changeStatus'])->name('invoices.changeStatus');

    Route::resource('/activity_log', ActivityLogController::class)->names('activity_log');
    Route::get('/activity_log/get/data', [ActivityLogController::class, 'getData'])->name('activity_log.data');
    Route::post('/activity_log/bulkDelete', [ActivityLogController::class, 'bulkDelete'])->name('activity_log.bulkDelete');

});
