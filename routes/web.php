<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminInventoryMovementController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/locale', [LocaleController::class, 'switch'])->name('locale.switch');

Auth::routes(['register' => false]);

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::get('products-search', [AdminProductController::class, 'search'])->name('products.search');
    Route::get('customers-search', [AdminCustomerController::class, 'search'])->name('customers.search');

    Route::resource('customers', AdminCustomerController::class);

    Route::resource('invoices', AdminInvoiceController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('invoices/{invoice}/print', [AdminInvoiceController::class, 'printInvoice'])->name('invoices.print');
    Route::get('invoices/{invoice}/pdf', [AdminInvoiceController::class, 'pdf'])->name('invoices.pdf');

    Route::get('payments/open-invoices', [AdminPaymentController::class, 'openInvoices'])->name('payments.open-invoices');
    Route::resource('payments', AdminPaymentController::class)->only(['index', 'create', 'store']);

    Route::get('inventory', [AdminInventoryMovementController::class, 'index'])->name('inventory.index');
    Route::get('inventory/create', [AdminInventoryMovementController::class, 'create'])->name('inventory.create');
    Route::post('inventory', [AdminInventoryMovementController::class, 'store'])->name('inventory.store');

    Route::get('reports/daily-sales', [AdminReportController::class, 'dailySales'])->name('reports.daily-sales');
    Route::get('reports/daily-sales.csv', [AdminReportController::class, 'dailySalesCsv'])->name('reports.daily-sales.csv');
    Route::get('reports/monthly-sales', [AdminReportController::class, 'monthlySales'])->name('reports.monthly-sales');
    Route::get('reports/profit', [AdminReportController::class, 'profit'])->name('reports.profit');
    Route::get('reports/profit.pdf', [AdminReportController::class, 'profitPdf'])->name('reports.profit.pdf');
    Route::get('reports/inventory', [AdminReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/inventory.csv', [AdminReportController::class, 'inventoryCsv'])->name('reports.inventory.csv');
    Route::get('reports/debts', [AdminReportController::class, 'debts'])->name('reports.debts');
    Route::get('reports/debts.csv', [AdminReportController::class, 'debtsCsv'])->name('reports.debts.csv');
    Route::get('reports/top-products', [AdminReportController::class, 'topProducts'])->name('reports.top-products');
    Route::get('reports/top-products.csv', [AdminReportController::class, 'topProductsCsv'])->name('reports.top-products.csv');
});
