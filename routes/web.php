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
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\UserController;


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

//login e registo
Route::middleware(['guest'])->group(function () {
    // Login e registo 
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');

        
});

Route::middleware(['auth'])->group(function () {
   // Logout
    Route::post('/logout', function () {
        Auth::logout();
        session()->forget('wishlist');
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('catalog');
    })->name('logout');
});

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

// Reenviar email de verificação
Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('status', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');


// Página para verificação de email pendente
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');



//Profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{user}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');
});


// User Management
Route::middleware(['auth', 'can:manageUsers'])->prefix('board/users')->name('board.users.')->group(function () {
    // Lista, criação, edição e remoção 
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::patch('/{user}/remove-photo', [UserController::class, 'removePhoto'])->name('removePhoto');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

    // Ações específicas (PATCH)
    Route::patch('/{user}/block', [UserController::class, 'toggleBlock'])->name('block');
    Route::patch('/{user}/cancel', [UserController::class, 'cancelMembership'])->name('cancel');
    Route::patch('/{user}/promote', [UserController::class, 'promote'])->name('promote');
    Route::patch('/{user}/demote', [UserController::class, 'demote'])->name('demote');
});


//card
Route::get('/card', [CardController::class, 'show'])->name('card.show')->middleware('auth');
Route::post('/card/credit', [CardController::class, 'credit'])->name('card.credit')->middleware('auth');

//recibo pdf
Route::get('/receipt/{order}', [\App\Http\Controllers\ReceiptController::class, 'download'])
     ->middleware(['auth', 'can:viewReceipt,order'])
     ->name('receipt.downloadReceipt');

// order details (for club members)    
Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])
    ->name('orders.show')
    ->middleware('auth');

// recommended for you
Route::get('/recommended', [RecommendationController::class, 'index'])->name('recommended');
Route::post('/recommended/feedback', [RecommendationController::class, 'storeFeedback'])
     ->middleware(['auth'])
     ->name('recommended.feedback');


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



//pending orders
Route::middleware(['auth', 'can:viewAny,App\Models\Order'])->group(function () {
    Route::get('/order/pending', [OrderController::class, 'pending'])->name('order.pending');
});


//pending orders details (only for employees and board members)
Route::get('/order/pending/{order}', [OrderController::class, 'showPendingDetails'])
    ->middleware(['auth', 'can:view,order']) 
    ->name('order.pending.details');


//only employees can mark as completed
Route::patch('/order/{order}/complete', [OrderController::class, 'complete'])
    ->middleware(['auth', 'can:complete,order'])
    ->name('order.complete');

//only board members can mark as canceled
Route::patch('/order/{order}/cancel', [OrderController::class, 'cancel'])
    ->middleware(['auth', 'can:cancel,order'])
    ->name('order.cancel');

// download receipt from email
Route::get('/receipt/download/{order}', [OrderController::class, 'downloadReceipt'])
    ->middleware(['auth', 'can:viewReceipt,order'])
    ->name('receipt.download');



// inventory management e supply orders 
Route::middleware(['auth'])->group(function () {
    // inventory management
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/{product}/adjust', [InventoryController::class, 'adjustForm'])->name('inventory.adjust');
    Route::post('/inventory/{product}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust.store');
    Route::get('/inventory/adjustments', [InventoryController::class, 'adjustments'])->name('inventory.adjustments');

    // supply orders
    Route::get('/inventory/supply-orders', [\App\Http\Controllers\SupplyOrderController::class, 'index'])->name('supply_orders.index');
    Route::get('/inventory/supply-orders/create', [\App\Http\Controllers\SupplyOrderController::class, 'create'])->name('supply_orders.create');
    Route::post('/inventory/supply-orders', [\App\Http\Controllers\SupplyOrderController::class, 'store'])->name('supply_orders.store');
    Route::post('/inventory/supply-orders/auto', [\App\Http\Controllers\SupplyOrderController::class, 'generateAutomatically'])->name('supply_orders.auto');
    Route::post('/inventory/supply-orders/{order}/complete', [\App\Http\Controllers\SupplyOrderController::class, 'markAsCompleted'])->name('supply_orders.complete');
    Route::delete('/inventory/supply-orders/{order}', [\App\Http\Controllers\SupplyOrderController::class, 'destroy'])->name('supply_orders.destroy');
});


