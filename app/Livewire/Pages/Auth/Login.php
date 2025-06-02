<?php

namespace App\Livewire\Pages\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $errorMessage = null;

    public function login()
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Obter o utilizador antes de tentar autenticar
        $user = \App\Models\User::where('email', $this->email)->first();

        // Verificar se está bloqueado
        if ($user && $user->blocked) {
            $this->errorMessage = 'Your account has been blocked.';
            return;
        }

        // Tentar autenticar
        if (Auth::attempt($credentials, false)) {
            session()->regenerate();

            // Wishlist merge
            $sessionWishlist = session('wishlist', []);
            $dbWishlist = Auth::user()->getWishlist();
            $mergedWishlist = array_unique(array_merge($dbWishlist, $sessionWishlist));
            Auth::user()->setWishlist($mergedWishlist);
            session(['wishlist' => $mergedWishlist]);

            return redirect()->intended(route('catalog'));
        }

        $this->errorMessage = 'Invalid credentials.';
    }

    public function render()
    {
        return view('livewire.pages.auth.login')->layout('layouts.app');
    }
}
