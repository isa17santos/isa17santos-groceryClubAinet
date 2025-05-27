<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShippingCostController;

Route::get('/', function () {
    return view('welcome');
});

//rota catalog visualization
Route::get('/', [CatalogController::class, 'index'])->name('catalog');

//rota carrinho
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'view'])->name('cart.view');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');


//rota login
Route::get('/login', function () {
    return 'Login page coming soon!';
})->name('login');

//dark e light mode
Route::post('/toggle-theme', function () {
    $theme = session('theme', 'light') === 'dark' ? 'light' : 'dark';
    session(['theme' => $theme]);
    return back();
})->name('toggle.theme');

//wishlist
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');


//ver o que é o admin? ver o que é o can:manage? 

Route::middleware(['auth', 'can:manage,App\Models\User'])->group(function () {
// rota de categorias
Route::resource('categories', CategoryController::class);

// rota de produtos
Route::resource('products', ProductController::class);

// rota de gestão da taxa de adesão
Route::get('settings/membership-fee', [SettingsController::class, 'edit'])->name('settings.edit');
Route::put('settings/membership-fee', [SettingsController::class, 'update'])->name('settings.update');

// rota de custos de envio
Route::resource('shipping-costs', ShippingCostController::class);
});


