<?php

use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\InvoiceController;
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
    Route::resource('invoice', InvoiceController::class);
    Route::get('invoice/{invoice}/download', [InvoiceController::class, 'download'])->name('invoice.download');
    Route::post('invoice/exists', [InvoiceController::class, 'exists'])->name('invoice.exists');
    Route::delete('invoice/bulk-destroy', [InvoiceController::class, 'destroyBulk'])->name('invoice.bulk-destroy');
    Route::post('invoice/bulk-download', [InvoiceController::class, 'downloadBulk'])->name('invoice.bulk-download');
    Route::post('invoice/upload', [InvoiceController::class, 'upload'])->name('invoice.upload');
});

require __DIR__.'/auth.php';
