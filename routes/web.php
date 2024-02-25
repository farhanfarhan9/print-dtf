<?php

use App\Livewire\Bank\AllBank;
use App\Livewire\Address\AllAddress;
use Illuminate\Support\Facades\Route;
use App\Livewire\Customer\AllCustomer;
use App\Livewire\Address\CreateAddress;
use App\Livewire\Address\EditAddress;
use App\Livewire\Customer\EditCustomer;
use App\Livewire\Customer\CreateCustomer;
use App\Livewire\Bank\CreateBankInformations;


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

    Route::prefix('profile')->group(function () {
        Route::view('/', 'profile')->name('profile');

        Route::get('/address', AllAddress::class)->name('address.index');
        Route::get('/address/create', CreateAddress::class)->name('address.create');
        Route::get('/address/{address_data}/edit', EditAddress::class)->name('address.edit');
    });

    Route::prefix('customers')->group(function () {
        Route::get('/', AllCustomer::class)->name('customer.index');
        Route::get('/create', CreateCustomer::class)->name('customer.create');
        Route::get('/{customer}/edit', EditCustomer::class)->name('customer.edit');
    });

    Route::prefix('bank-informations')->group(function () {
        Route::get('/', AllBank::class)->name('bank.index');
        Route::get('/create', CreateBankInformations::class)->name('bank.create');
        // Route::get('/{customer}/edit', EditCustomer::class)->name('customer.edit');
    });
});

Route::get('/products', \App\Livewire\Product::class)->name('products-view');
Route::get('/products/add', \App\Livewire\ProductAdd::class)->name('product.add');
Route::get('/product/edit/{product}', \App\Livewire\ProductEdit::class)->name('product-edit');

Route::get('/ekspedisi', \App\Livewire\Ekspedisi\Ekspedisis::class)->name('ekspedisi-view');
Route::get('/ekspedisi/add', \App\Livewire\Ekspedisi\EkspedisiAdd::class)->name('ekspedisi.add');
Route::get('/ekspedisi/edit/{ekspedisi}', \App\Livewire\Ekspedisi\EkspedisiEdit::class)->name('ekspedisi-edit');


require __DIR__.'/auth.php';
