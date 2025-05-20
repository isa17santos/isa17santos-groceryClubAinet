<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;

Route::get('/', function () {
    return view('welcome');
});

//rota catalog visualization
Route::get('/', [CatalogController::class, 'index'])->name('catalog');

//rota carrinho
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'view'])->name('cart.view');


//rota login
Route::get('/login', function () {
    return 'Login page coming soon!';
})->name('login');
