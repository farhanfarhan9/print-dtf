<?php

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


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/products', \App\Livewire\Product::class)->name('products-view');
Route::get('/products/add', \App\Livewire\ProductAdd::class)->name('product.add');
Route::get('/product/edit/{product}', \App\Livewire\ProductEdit::class)->name('product-edit');

Route::get('/ekspedisi', \App\Livewire\Ekspedisi\Ekspedisis::class)->name('ekspedisi-view');
Route::get('/ekspedisi/add', \App\Livewire\Ekspedisi\EkspedisiAdd::class)->name('ekspedisi.add');
Route::get('/ekspedisi/edit/{ekspedisi}', \App\Livewire\Ekspedisi\EkspedisiEdit::class)->name('ekspedisi-edit');


require __DIR__.'/auth.php';
