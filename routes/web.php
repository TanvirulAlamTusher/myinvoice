<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\BackupRestoreController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BusinessSettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceTemplateController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReturnController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/reset-password', 'resetPassword')->name('password.reset');
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register');
});

Route::middleware('auth')->controller(AuthController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');

});

Route::middleware(['auth'])->group(function () {

    Route::resource('roles', RoleController::class);

});

Route::middleware('auth', 'throttle:60,1')->controller(DashboardController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/dashboard', 'index')->name('dashboard');

});

Route::middleware(['auth', 'throttle:60,1'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | USERS ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {

        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');

        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');

        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | PROFILE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/update', 'update')->name('update');
        Route::put('/change-password', 'changePassword')->name('changePassword');
    });

    /*
    |--------------------------------------------------------------------------
    | CATEGORY ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('category')->name('category.')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | BRAND ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('brands')->name('brands.')->controller(BrandController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}/edit', 'edit')->name('edit');

        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');

        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | PRODUCT UNITS ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('product-units')->name('product-units.')->controller(ProductUnitController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | PRODUCT ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('products')->name('products.')->controller(ProductController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        // STOCK
        Route::post('/{id}/increase-stock', 'increaseStock')->name('increaseStock');

        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');

        Route::get('/{id}', 'show')->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('customers')->name('customers.')->controller(CustomerController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');

        Route::get('/{customer}', 'show')->name('show');
        Route::put('/{customer}', 'update')->name('update');
        Route::delete('/{customer}', 'destroy')->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | INVOICE ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('invoices')->name('invoices.')->controller(InvoiceController::class)->group(function () {
        Route::get('/', 'index')->name('index');

        Route::get('/trash', 'trash')->name('trash'); // 👈 ADD THIS

        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');

        Route::get('/{invoice}/edit', 'edit')->name('edit');
        Route::put('/{invoice}', 'update')->name('update');

        Route::delete('/{invoice}', 'destroy')->name('destroy');

        Route::get('/{invoice}', 'show')->name('show');

        // 👇 TRASH ACTIONS
        Route::post('/{id}/restore', 'restore')->name('restore');
        // 👇 TRASH DELETE
        Route::delete('/{id}/force-delete', 'forceDelete')->name('forceDelete');
        Route::delete('/trash/delete-selected', 'forceDeleteSelected')->name('forceDeleteSelected');
        Route::delete('/trash/delete-all','forceDeleteAll')->name('forceDeleteAll');

        // 👇 INVOICE PRINT
        Route::get('/{invoice}/print', 'printInvoice')->name('print');
        // 👇 INVOICE pdf download
        Route::get('/{invoice}/pdf', 'downloadPdf')->name('pdf');
       Route::get('/{invoice}/pdf-file', 'sharePdfFile')
    ->name('pdf.file');

    });


 /*
    |--------------------------------------------------------------------------
    | PRODUCT RETURN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::post('invoices/{invoice}/returns', [ProductReturnController::class, 'storeReturn'])
     ->name('invoices.returns.store');

     Route::delete('invoices/{invoice}/returns', [ProductReturnController::class, 'destroyReturn'])
     ->name('invoices.returns.destroy');

    /*
    |--------------------------------------------------------------------------
    | Business Settings Routes (single record)
    |--------------------------------------------------------------------------
    */
    Route::prefix('business-settings')->name('business-settings.')->controller(BusinessSettingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/save', 'save')->name('save');
        Route::delete('/destroy', 'destroy')->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | invoice templates routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('invoice-templates')->name('invoice-templates.')->controller(InvoiceTemplateController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'view')->name('view');
        Route::patch('/{template}/toggle-default', 'toggleDefault')->name('toggle-default');
        Route::patch('/{template}/toggle-status', 'toggleStatus')->name('toggle-status');

    });

     /*
    |--------------------------------------------------------------------------
    | backup and restore routes
    |--------------------------------------------------------------------------
    */
    Route::controller(BackupRestoreController::class)->group(function () {
        Route::get('/backup-restore', 'index')->name('backup-restore.index');
        Route::get('/database/download','dbDownload')->name('db.download');
        Route::post('/database/restore', 'dbRestore')->name('db.restore');



    });
});
