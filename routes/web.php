<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StockMovementController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Master Data (Admin & Super Admin)
    Route::middleware(['role:Super Admin|Admin'])->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('items', ItemController::class);
    });

    // Customers (Kasir, Admin, Super Admin)
    Route::middleware(['role:Super Admin|Admin|Kasir'])->group(function () {
        Route::resource('customers', CustomerController::class);
    });

    // Sales Orders (Kasir, Admin, Super Admin)
    Route::middleware(['role:Super Admin|Admin|Kasir'])->group(function () {
        Route::resource('sales-orders', SalesOrderController::class);
        Route::get('sales-orders/{sales_order}/surat-jalan', [SalesOrderController::class, 'suratJalan'])->name('sales-orders.surat-jalan');
        Route::get('sales-orders/{sales_order}/faktur', [SalesOrderController::class, 'faktur'])->name('sales-orders.faktur');
        Route::post('sales-orders/{sales_order}/confirm', [SalesOrderController::class, 'confirm'])->name('sales-orders.confirm');
    });

    // Purchase Orders (Gudang, Admin, Super Admin)
    Route::middleware(['role:Super Admin|Admin|Gudang'])->group(function () {
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::post('purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
        Route::get('purchase-orders/{purchase_order}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');
    });

    // Stock Movements (Admin, Super Admin, Gudang)
    Route::middleware(['role:Super Admin|Admin|Gudang'])->group(function () {
        Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    });
});

require __DIR__.'/auth.php';
