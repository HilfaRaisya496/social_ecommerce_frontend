<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
})->name('login');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/register', [AuthController::class, 'registerSeller'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google Login Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Profile Routes
Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

use App\Http\Controllers\VoucherController;

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'adminIndex'])->name('admin.dashboard');
    Route::get('/users', [DashboardController::class, 'adminUsers'])->name('admin.users');
    Route::get('/buyers', [DashboardController::class, 'adminBuyers'])->name('admin.buyers');
    Route::get('/users/{id}', [DashboardController::class, 'adminUserShow'])->name('admin.users.show');
    Route::get('/sellers', [DashboardController::class, 'adminSellers'])->name('admin.sellers');
    Route::get('/products', [DashboardController::class, 'adminProducts'])->name('admin.products');
    Route::get('/products/{id}', [DashboardController::class, 'adminProductShow'])->name('admin.products.show');
    Route::get('/transactions', [DashboardController::class, 'adminTransactions'])->name('admin.transactions');
    Route::get('/transactions/{id}', [DashboardController::class, 'adminTransactionShow'])->name('admin.transactions.show');
    Route::post('/transactions/{id}/confirm', [DashboardController::class, 'adminConfirmPayment'])->name('admin.transactions.confirm');
    Route::get('/vouchers', [VoucherController::class, 'index'])->name('admin.vouchers');
    Route::get('/vouchers/create', [VoucherController::class, 'create'])->name('admin.vouchers.create');
    Route::post('/vouchers', [VoucherController::class, 'store'])->name('admin.vouchers.store');
    Route::get('/vouchers/{id}/edit', [VoucherController::class, 'edit'])->name('admin.vouchers.edit');
    Route::put('/vouchers/{id}', [VoucherController::class, 'update'])->name('admin.vouchers.update');
    Route::delete('/vouchers/{id}', [VoucherController::class, 'destroy'])->name('admin.vouchers.destroy');

    Route::get('/settings', [DashboardController::class, 'adminSettings'])->name('admin.settings');
    Route::get('/chats', [DashboardController::class, 'adminChats'])->name('admin.chats');
    Route::delete('/users/{id}', [DashboardController::class, 'deleteUser'])->name('admin.users.delete');
    Route::delete('/products/{id}', [DashboardController::class, 'adminDeleteProduct'])->name('admin.products.delete');
    Route::get('/withdrawals', [DashboardController::class, 'adminWithdrawals'])->name('admin.withdrawals');
    Route::post('/withdrawals/{id}/approve', [DashboardController::class, 'adminApproveWithdrawal'])->name('admin.withdrawals.approve');
    Route::post('/withdrawals/{id}/reject', [DashboardController::class, 'adminRejectWithdrawal'])->name('admin.withdrawals.reject');
});

// Seller Routes
Route::prefix('seller')->group(function () {
    Route::get('/', [DashboardController::class, 'sellerIndex'])->name('seller.dashboard');
    Route::get('/products', [DashboardController::class, 'sellerProducts'])->name('seller.products');
    Route::get('/products/add', [DashboardController::class, 'sellerAddProduct'])->name('seller.add_product');
    Route::post('/products/add', [DashboardController::class, 'sellerStoreProduct'])->name('seller.add_product.store');
    Route::get('/products/{id}/edit', [DashboardController::class, 'sellerEditProduct'])->name('seller.products.edit');
    Route::put('/products/{id}', [DashboardController::class, 'sellerUpdateProduct'])->name('seller.products.update');
    Route::delete('/products/{id}', [DashboardController::class, 'sellerDeleteProduct'])->name('seller.products.delete');
    Route::get('/orders', [DashboardController::class, 'sellerOrders'])->name('seller.orders');
    Route::get('/orders/{id}', [DashboardController::class, 'sellerOrderShow'])->name('seller.orders.show');
    Route::post('/orders/{id}/status', [DashboardController::class, 'sellerUpdateOrderStatus'])->name('seller.orders.update_status');
    Route::get('/chats', [DashboardController::class, 'sellerChats'])->name('seller.chats');
    Route::get('/transactions', [DashboardController::class, 'sellerTransactions'])->name('seller.transactions');
    Route::get('/reviews', [DashboardController::class, 'sellerReviews'])->name('seller.reviews');
    Route::post('/reviews/{id}/reply', [DashboardController::class, 'sellerReplyReview'])->name('seller.reviews.reply');
    Route::get('/settings', [DashboardController::class, 'sellerSettings'])->name('seller.settings');
    Route::get('/withdrawals', [DashboardController::class, 'sellerWithdrawals'])->name('seller.withdrawals');
    Route::post('/withdrawals', [DashboardController::class, 'sellerStoreWithdrawal'])->name('seller.withdrawals.store');
});
