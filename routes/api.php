<?php

use App\Http\Controllers\Home\CustomerConrtoller;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('auth')->middleware('guest')->group(function(){
    Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::controller(CustomerConrtoller::class)->group(function(){
        Route::get('dashboard', 'displayDashboard')->middleware(['verified'])->name('dashboard');
        Route::get('create-wallet', 'displayCreateWalletPage')->middleware(['verified'])->name('show-create-wallet-page');
        Route::post('store-user-wallet', 'createNewWallet')->name('post-store-user-wallet');
        Route::get('transfer', 'displayTransferPage')->name('show-transfer-page');
        Route::post('send-funds', 'makeFundTransfer')->name('post-transfer-funds');
    });
});