<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;

use App\Http\Controllers\DashboardController;
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('categories', CategoryController::class);
Route::resource('suppliers', SupplierController::class);
Route::resource('customers', CustomerController::class);
Route::resource('items', ItemController::class);

use App\Http\Controllers\SalesOrderController;
Route::resource('sales-orders', SalesOrderController::class);
Route::get('sales-orders/{sales_order}/surat-jalan', [SalesOrderController::class, 'suratJalan'])->name('sales-orders.surat-jalan');
Route::get('sales-orders/{sales_order}/faktur', [SalesOrderController::class, 'faktur'])->name('sales-orders.faktur');
Route::post('sales-orders/{sales_order}/confirm', [SalesOrderController::class, 'confirm'])->name('sales-orders.confirm');

use App\Http\Controllers\PurchaseOrderController;
Route::resource('purchase-orders', PurchaseOrderController::class);
Route::post('purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
Route::get('purchase-orders/{purchase_order}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');

use App\Http\Controllers\StockMovementController;
Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
