<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

Route::get('stripe', [SellerController::class, 'save'])->name('save.express');

Route::group(['middleware' => ['auth:web']], function () {
    Route::group(['middleware' => ['stripe']], function () {
        Route::get('/', [ProductController::class, 'index'])->name('products');
        Route::get('dashboard', [SellerController::class, 'login'])->name('stripe.login');
        Route::get('add', [ProductController::class, 'add'])->name('product.form')->middleware('seller');
        Route::post('add', [ProductController::class, 'store'])->name('save.product')->middleware('seller');
        Route::post('purchase', [ProductController::class, 'purchase'])->name('purchase')->middleware('customer');
    });
    Route::get('save', [CustomerController::class, 'form'])->name('stripe.form');
    Route::post('save', [CustomerController::class, 'save'])->name('save.customer');
    Route::get('express', [SellerController::class, 'create'])->name('create.express');
});
