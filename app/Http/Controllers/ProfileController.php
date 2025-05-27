<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ProfileController extends Controller
{
    use AuthorizesRequests;

    public function show(User $user)
    {
        $this->authorize('update', $user);

        if ($user->type === 'employee') {
            return view('profile.show_employee', compact('user'));
        }

        return view('profile.show', compact('user'));
    }

    
    //Formulário para editar perfil do utilizador.
   public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('profile.edit', compact('user'));
    }
  
    //Atualiza os dados do perfil do utilizador.
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $rules = [
            'name' => 'required|string|max:255',
            'gender' => ['required', Rule::in(['M', 'F'])],
            'photo' => 'nullable|image|max:2048'
        ];

        if (in_array($user->type, ['member', 'board'])) {
            $rules += [
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($user->id),
                ],
                'nif' => 'nullable|digits:9',
                'default_delivery_address' => 'nullable|string|max:255',
                'default_payment_type' => ['nullable', Rule::in(['Visa', 'PayPal', 'MB WAY'])],
            ];

             // Validação dinâmica da referência de pagamento
            $paymentType = $request->input('default_payment_type');

            if ($paymentType === 'Visa') {
                $rules['default_payment_reference'] = ['required', 'numeric', 'digits:16'];
            } elseif ($paymentType === 'PayPal') {
                $rules['default_payment_reference'] = ['required', 'email'];
            } elseif ($paymentType === 'MB WAY') {
                $rules['default_payment_reference'] = ['required', 'regex:/^9\d{8}$/'];
            }
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);  
            }
            $validated['photo'] = basename($request->file('photo')->store('users', 'public'));
        }

        $user->update($validated);

        return redirect()
            ->route('profile.edit', $user)
            ->with('success', 'Profile updated successfully');
    }
}
