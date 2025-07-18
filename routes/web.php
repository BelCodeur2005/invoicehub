<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProformaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;

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



Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])
    ->name('password.email');

Route::get('/verify-otp', [ForgotPasswordController::class, 'showVerifyOtpForm'])
    ->name('password.verify-otp');

Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])
    ->name('password.verify-otp.submit');

Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])
    ->name('password.update');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');
    // Supprimer l'avatar
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

    // Clients
    Route::get('/clients/export', [ClientController::class, 'export'])->name('clients.export');
    Route::resource('clients', ClientController::class);
    // Products
    Route::resource('products', ProductController::class);

    // Proformas
    Route::resource('proformas', ProformaController::class);
    Route::post('/proformas/{proforma}/convert', [ProformaController::class, 'convertToInvoice'])
         ->name('proformas.convert');
    Route::get('/proformas/{proforma}/pdf', [ProformaController::class, 'generatePdf'])
    ->name('proformas.pdf');
    Route::post('/proformas/{proforma}/send', [ProformaController::class, 'send'])
    ->name('proformas.send');
    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::patch('/invoices/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])
         ->name('invoices.mark-as-paid');
    Route::patch('/invoices/{invoice}/mark-as-sent', [InvoiceController::class, 'markAsSent'])
         ->name('invoices.mark-as-sent');
    Route::patch('/invoices/{invoice}/mark-as-cancelled', [InvoiceController::class, 'markAsCancelled'])
         ->name('invoices.mark-as-cancelled');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])
    ->name('invoices.pdf');
    Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])
    ->name('invoices.send');
    Route::get('/invoices/{invoice}/status-history', [InvoiceController::class, 'showStatusHistory'])
    ->name('invoices.status-history');
    // Users (accès restreint)
    Route::middleware(['check.user.role:admin,manager'])->group(function () {
        Route::resource('users', UserController::class);
        // routes/web.php
        Route::delete('/users/{user}/delete-avatar', [UserController::class, 'deleteAvatar'])
            ->name('users.delete-avatar');
            });
});

require __DIR__.'/auth.php';
