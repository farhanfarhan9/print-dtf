<?php

use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\ExpeditionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankController;
use App\Http\Controllers\ViewKodePosController;

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

Route::get('bank/index', [BankController::class, 'index'])->name('api.bank.index');
Route::get('customer/allCustomer', [CustomerController::class, 'index'])->name('api.customers.index');
Route::get('expedition/allExpeditions', [ExpeditionController::class, 'index'])->name('api.expeditions.index');

Route::get('/kode-pos', [ViewKodePosController::class, 'index'])->name('api.kode-pos.index');
