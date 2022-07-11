<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\Admin\ProductController;
use App\Shop;
use GuzzleHttp\Client;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ShopifyController::class, 'getShopName']);
Route::get('/shopify', [ShopifyController::class, 'shopify'])->name('shopify');
Route::get('authen', [ShopifyController::class, 'authen']);

Route::group(['prefix' => 'admin', 'middleware' => 'shop.login'], function () {
    Route::get('products', [ProductController::class, 'index'])->name('products');
    Route::get('product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('product/store', [ProductController::class, 'store'])->name('product.store');
    Route::get('product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('product/update', [ProductController::class, 'update'])->name('product.update');
    Route::post('product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');
});

Route::get('create', [ShopifyController::class, 'createProduct'])->name('create');


// Route::get('queue', [ShopifyController::class, 'queue'])->name('queue');

// Route::get('ehe', function(){
//     $shop = Shop::where('id' , '=', 6565)->first();
//     dd(is_null($shop));
// });

// Route::get('test', function(){
//     // session(['key' => 'value']);
//     session()->flash('name', 'Linh');
//     // session()->forget('name');
//     // dd(session()->get('name'));
//     dd(session()->has('name'));

//     return redirect()->route('test2');
// });
// Route::get('test2', function(){
//     dd(session()->get('name'));
// })->name('test2');

// Route::any('webhook', [ShopifyController::class, 'webhook']);