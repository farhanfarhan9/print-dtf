<?php

use App\Livewire\Product;
use App\Livewire\ProductAdd;
use App\Livewire\ProductEdit;
use App\Livewire\Bank\AllBank;
use App\Livewire\Order\AllOrder;
use App\Livewire\Order\Po\AllPo;
use App\Livewire\Order\Po\EditPo;
use App\Livewire\Order\CreateOrder;
use App\Livewire\Address\AllAddress;
use App\Livewire\Address\EditAddress;
use Illuminate\Support\Facades\Route;
use App\Livewire\Customer\AllCustomer;
use App\Livewire\CustomerImportData;
use App\Livewire\Ekspedisi\Ekspedisis;
use App\Livewire\Address\CreateAddress;
use App\Livewire\Customer\EditCustomer;
use App\Livewire\Ekspedisi\EkspedisiAdd;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\isAdminMiddleware;
use App\Livewire\Customer\CreateCustomer;
use App\Livewire\Ekspedisi\EkspedisiEdit;
use App\Livewire\Bank\CreateBankInformations;
use App\Livewire\Customer\ArchieveCustomer;
use App\Livewire\Dashboard;
use App\Livewire\Ekspedisi\EkspedisiArchieve;
use App\Livewire\InternalProcess\AllInternalProcess;
use App\Livewire\ExportData\ExportCustomerView;
use App\Livewire\ExportData\ExportProductView;
use App\Livewire\ExportData\ExportBookkeepingView;
use App\Livewire\InternalProcess\HistoryInternalProcess;
use App\Livewire\User\AllUser;
use App\Livewire\User\ArchieveUser;
use App\Livewire\User\CreateUser;
use App\Livewire\User\EditUser;

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

// Route::view('/', 'welcome');
Route::view('/tem', 'temdashboard');


Route::middleware(['auth'])->group(function () {
    Route::get('/print-shipping-label/{orderId}', [OrderController::class, 'printShippingLabel'])->name('print.shipping.label');
    Route::get('/view-shipping-label/{orderId}', [OrderController::class, 'viewShippingLabel'])->name('view.shipping.label');
    Route::get('/print-invoice-label/{orderId}', [OrderController::class, 'printInvoiceLabel'])->name('print.invoice.label');
    Route::get('/view-invoice-label/{orderId}', [OrderController::class, 'viewInvoiceLabel'])->name('view.invoice.label');


    Route::get('/', Dashboard::class)->name('dashboard');

    Route::prefix('profile')->group(function () {
        Route::view('/', 'profile')->name('profile');

        Route::get('/address', AllAddress::class)->name('address.index');
        Route::get('/address/create', CreateAddress::class)->name('address.create');
        Route::get('/address/{address_data}/edit', EditAddress::class)->name('address.edit');
    });

    Route::prefix('customers')->group(function () {
        Route::get('/', AllCustomer::class)->name('customer.index')->middleware(isAdminMiddleware::class);
        Route::get('/create', CreateCustomer::class)->name('customer.create')->middleware(isAdminMiddleware::class);
        Route::get('/upload', CustomerImportData::class)->name('customer.upload')->middleware(isAdminMiddleware::class);
        Route::get('/{customer}/edit', EditCustomer::class)->name('customer.edit')->middleware(isAdminMiddleware::class);
        Route::get('/archieve', ArchieveCustomer::class)->name('customer.archieve')->middleware(isAdminMiddleware::class);
    });
    Route::prefix('users')->group(function () {
        Route::get('/', AllUser::class)->name('user.index')->middleware(isAdminMiddleware::class);
        Route::get('/create', CreateUser::class)->name('user.create')->middleware(isAdminMiddleware::class);
        Route::get('/{user}/edit', EditUser::class)->name('user.edit')->middleware(isAdminMiddleware::class);
        Route::get('/archieve', ArchieveUser::class)->name('user.archieve')->middleware(isAdminMiddleware::class);
    });

    Route::prefix('bank-informations')->group(function () {
        Route::get('/', AllBank::class)->name('bank.index');
        Route::get('/create', CreateBankInformations::class)->name('bank.create');
        // Route::get('/{customer}/edit', EditCustomer::class)->name('customer.edit');
    });

    Route::get('/products', Product::class)->name('products-view')->middleware(isAdminMiddleware::class);
    Route::get('/products/add', ProductAdd::class)->name('product.add')->middleware(isAdminMiddleware::class);
    Route::get('/product/edit/{product}', ProductEdit::class)->name('product-edit')->middleware(isAdminMiddleware::class);

    Route::get('/ekspedisi', Ekspedisis::class)->name('ekspedisi-view')->middleware(isAdminMiddleware::class);
    Route::get('/ekspedisi/add', EkspedisiAdd::class)->name('ekspedisi.add')->middleware(isAdminMiddleware::class);
    Route::get('/ekspedisi/edit/{ekspedisi}', EkspedisiEdit::class)->name('ekspedisi-edit')->middleware(isAdminMiddleware::class);
    Route::get('/ekspedisi/archieve', EkspedisiArchieve::class)->name('ekspedisi-archieve')->middleware(isAdminMiddleware::class);

    Route::prefix('orders')->group(function () {
        Route::get('/', AllOrder::class)->name('order.index')->middleware(isAdminMiddleware::class);
        Route::get('/create', CreateOrder::class)->name('order.create')->middleware(isAdminMiddleware::class);

        Route::get('/{order}/purchase_order', AllPo::class)->name('po.allPo')->middleware(isAdminMiddleware::class);
        Route::get('/{order}/purchase_order/{po}/edit', EditPo::class)->name('po.editPo')->middleware(isAdminMiddleware::class);
    });

    Route::prefix('export-data')->group(function () {
        Route::get('/customer', ExportCustomerView::class)->name('export-customer.index');
        Route::get('/product', ExportProductView::class)->name('export-product.index');
        Route::get('/bookkeeping', ExportBookkeepingView::class)->name('export-bookkeeping.index');
    });

    Route::prefix('internal_process')->group(function () {
        Route::get('/', AllInternalProcess::class)->name('internal_process.index');
        Route::get('/history', HistoryInternalProcess::class)->name('history_internal_process.index');
        // Route::get('/create', CreateOrder::class)->name('order.create');

        // Route::get('/{order}/purchase_order', AllPo::class)->name('po.allPo');
        // Route::get('/{order}/purchase_order/{po}/edit', EditPo::class)->name('po.editPo');
    });
});



require __DIR__ . '/auth.php';
