<?php

use App\Livewire\Product;
use App\Livewire\ProductAdd;
use App\Livewire\ProductEdit;
use App\Livewire\Bank\AllBank;
use App\Livewire\Address\AllAddress;
use App\Livewire\Address\EditAddress;
use Illuminate\Support\Facades\Route;
use App\Livewire\Customer\AllCustomer;
use App\Livewire\Ekspedisi\Ekspedisis;
use App\Livewire\Address\CreateAddress;
use App\Livewire\Customer\EditCustomer;
use App\Livewire\Ekspedisi\EkspedisiAdd;
use App\Livewire\Customer\CreateCustomer;
use App\Livewire\Ekspedisi\EkspedisiEdit;
use App\Livewire\Bank\CreateBankInformations;
use App\Livewire\Order\AllOrder;
use App\Livewire\Order\CreateOrder;
use App\Livewire\Order\Po\AllPo;
use App\Livewire\Order\Po\EditPo;

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

    Route::get('/products', Product::class)->name('products-view');
    Route::get('/products/add', ProductAdd::class)->name('product.add');
    Route::get('/product/edit/{product}', ProductEdit::class)->name('product-edit');

    Route::get('/ekspedisi', Ekspedisis::class)->name('ekspedisi-view');
    Route::get('/ekspedisi/add', EkspedisiAdd::class)->name('ekspedisi.add');
    Route::get('/ekspedisi/edit/{ekspedisi}', EkspedisiEdit::class)->name('ekspedisi-edit');

    Route::prefix('orders')->group(function () {
        Route::get('/', AllOrder::class)->name('order.index');
        Route::get('/create', CreateOrder::class)->name('order.create');

        Route::get('/{order}/purchase_order', AllPo::class)->name('po.allPo');
        Route::get('/{order}/purchase_order/{po}/edit', EditPo::class)->name('po.editPo');

    });
});



require __DIR__ . '/auth.php';
