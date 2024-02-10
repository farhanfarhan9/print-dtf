<?php

use App\Livewire\Customer\AllCustomer;
use App\Livewire\Customer\CreateCustomer;
use App\Livewire\Customer\EditCustomer;
use Illuminate\Support\Facades\Route;

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

Route::view('/', 'welcome');
Route::view('/tem', 'temdashboard');


Route::middleware(['auth'])->group(function () {

    Route::view('dashboard', 'dashboard')
        ->middleware('verified')
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->name('profile');

    Route::prefix('customers')->group(function(){
        Route::get('/', AllCustomer::class)->name('customer.index');
        Route::get('/create', CreateCustomer::class)->name('customer.create');
        Route::get('/{customer}/edit', EditCustomer::class)->name('customer.edit');
    });
});

require __DIR__ . '/auth.php';
