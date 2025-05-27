<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\WishlistController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\RecommendationController;
use App\Livewire\Pages\Auth\Login;
use App\Livewire\Pages\Auth\Register;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShippingCostController;


//rota catalog visualization
Route::get('/', [CatalogController::class, 'index'])->name('catalog');

//rota carrinho
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'view'])->name('cart.view');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

//wishlist
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');



// Login e registo 
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::get('/register', [RegisterController::class, 'show'])->name('register');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('catalog');
})->name('logout');

//change password (rota protegida)
Route::get('/changePassword', [ChangePasswordController::class, 'show'])
    ->middleware('auth')
    ->name('changePassword');

//forgot password
Route::get('forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');


//membership confirmation
Route::get('/membershipConfirmation', [MembershipController::class, 'show'])->middleware('auth')->name('membership');    


// Página para verificação de email pendente
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


// Reenviar email de verificação
Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('status', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Rotas para utilizadores autenticados e verificados
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Exemplo: rota para pagar inscrição
    Route::get('/membership/confirm', \App\Livewire\Pages\Membership\Confirm::class)->name('membership.confirm');
});

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');


//Profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{user}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');
});




//card
Route::get('/card', [CardController::class, 'show'])->name('card.show')->middleware('auth');
Route::post('/card/credit', [CardController::class, 'credit'])->name('card.credit')->middleware('auth');

//recibo pdf
Route::get('/receipt/{order}', [\App\Http\Controllers\ReceiptController::class, 'download'])
    ->middleware('auth')
    ->name('receipt.download');

// order details    
Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])
    ->name('orders.show')
    ->middleware('auth');

// recommended for you
Route::get('/recommended', [RecommendationController::class, 'index'])->name('recommended');

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

