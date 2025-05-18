<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;

Route::get('/', function () {
    return view('welcome');
});

//rota catalog visualization
Route::get('/', [CatalogController::class, 'index']);

//rota carrinho
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
