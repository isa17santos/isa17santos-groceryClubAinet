<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use App\Models\Card;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Auth\Events\Registered;

class Register extends Component
{
    use WithFileUploads;

    public $name, $email, $password, $gender;
    public $delivery_address, $nif;
    public $payment_type, $payment_reference;
    public $photo;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'gender' => 'required|in:M,F',
            'delivery_address' => 'nullable|string|max:255',
            'nif' => 'nullable|digits:9',
            'photo' => 'nullable|image|max:1024',
            'payment_type' => 'nullable|in:Visa,PayPal,MB WAY',
            'payment_reference' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if (!$this->payment_type || !$value) return;

                if ($this->payment_type === 'Visa' && !preg_match('/^\d{16}$/', $value)) {
                    $fail('Visa reference must be 16 digits.');
                } elseif ($this->payment_type === 'PayPal' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail('PayPal reference must be a valid email address.');
                } elseif ($this->payment_type === 'MB WAY' && !preg_match('/^9\d{8}$/', $value)) {
                    $fail('MB WAY reference must start with 9 and have 9 digits.');
                }
            }],
        ];
    }


    public function register()
    {
        $this->validate();

        if ($this->photo) {
            $photoName = uniqid() . '.' . $this->photo->getClientOriginalExtension();
            $this->photo->storeAs('users', $photoName, 'public'); 
        }
        
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'gender' => $this->gender,
            'photo' => $photoName,
            'nif' => $this->nif,
            'default_delivery_address' => $this->delivery_address,
            'type' => 'pending_member',
            'blocked' => false,
            'default_payment_type' => $this->payment_type,
            'default_payment_reference' => $this->payment_reference,
        ]);

        // Criar cartão virtual
        Card::create([
            'id' => $user->id,
            'card_number' => random_int(100000, 999999),
            'balance' => 0,
        ]);

        // Enviar email de verificação
        event(new Registered($user));

        session()->flash('status', 'Registration successful! Please check your email to verify your account.');
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.pages.auth.register')->layout('layouts.app');
    }
}
