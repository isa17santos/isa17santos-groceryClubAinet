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

        if (Auth::attempt($credentials, false)) {
            session()->regenerate();
            $user = Auth::user();

            //Merge da wishlist da sessão com a da base de dados
            $sessionWishlist = session('wishlist', []);
            $dbWishlist = $user->getWishlist();

            $mergedWishlist = array_unique(array_merge($dbWishlist, $sessionWishlist));

            //Guardar wishlist unificada
            $user->setWishlist($mergedWishlist);

            //Atualizar sessão
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
