<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        // Verifica se o hash do email bate certo
        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        // Autentica o utilizador
        Auth::login($user);

        // Se já verificado, redireciona
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('membership');
        }

        // Marca como verificado
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('membership');
    }
}
