<?php

use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\InvoiceController;
use App\Http\Controllers\Client\SalaryController;
use App\Http\Controllers\Client\KilometersController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/template', function () {
    return view('wowdash.dashboard.index');
});

// Language switching route
Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'pt'])) {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }
    return redirect()->back();
})->name('language.switch');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Invoice routes
    Route::get('invoice/scan', [InvoiceController::class, 'scan'])->name('invoice.scan');
    Route::resource('invoice', InvoiceController::class);
    Route::get('invoice/{invoice}/download', [InvoiceController::class, 'download'])->name('invoice.download');
    Route::post('invoice/exists', [InvoiceController::class, 'exists'])->name('invoice.exists');
    Route::delete('invoice/bulk-destroy', [InvoiceController::class, 'destroyBulk'])->name('invoice.bulk-destroy');
    Route::post('invoice/bulk-download', [InvoiceController::class, 'downloadBulk'])->name('invoice.bulk-download');
    Route::post('invoice/upload', [InvoiceController::class, 'upload'])->name('invoice.upload');

    // Route::post('/processar-qr-simples', [App\Http\Controllers\Client\InvoiceController::class, 'processSimpleQr'])->name('invoice.process-simple-qr');
    // Route::post('/guardar-fatura-simples', [App\Http\Controllers\Client\InvoiceController::class, 'storeSimpleInvoice'])->name('invoice.store-simple');

    // Salary routes
    Route::resource('salary', SalaryController::class);
    Route::delete('salary/bulk-destroy', [SalaryController::class, 'destroyBulk'])->name('salary.bulk-destroy');
    Route::get('salary/stats', [SalaryController::class, 'getStats'])->name('salary.stats');

    // Kilometer routes
    Route::resource('kilometer', KilometersController::class);
    Route::delete('kilometer/bulk-destroy', [KilometersController::class, 'destroyBulk'])->name('kilometer.bulk-destroy');
    Route::get('kilometer/stats', [KilometersController::class, 'getStats'])->name('kilometer.stats');
});

require __DIR__.'/auth.php';
