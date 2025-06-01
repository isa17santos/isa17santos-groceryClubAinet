<?php

namespace App\Livewire\Pages\Membership;

use Livewire\Component;
use App\Models\Operation;
use Illuminate\Support\Facades\Auth;
use App\Services\Payment;
use App\Models\Settings;

class Confirm extends Component
{
    public $payment_type = 'visa';
    public $payment_reference;

    public function pay()
    {
        $user = Auth::user();
        $card = $user->card;
        $fee = Settings::first()->membership_fee;

        $rules = ['payment_type' => 'required|in:visa,paypal,mb way'];

        if ($this->payment_type === 'visa') {
            $rules['payment_reference'] = 'required|digits:16';
        } elseif ($this->payment_type === 'paypal') {
            $rules['payment_reference'] = 'required|email';
        } elseif ($this->payment_type === 'mb way') {
            $rules['payment_reference'] = 'required|regex:/^9\d{8}$/';
        }

        $this->validate($rules);

        $confirmed = match ($this->payment_type) {
            'visa' => Payment::payWithVisa($this->payment_reference, '123'),
            'paypal' => Payment::payWithPayPal($this->payment_reference),
            'mb way' => Payment::payWithMBway($this->payment_reference),
        };

        if (!$confirmed) {
            session()->flash('error', 'Payment failed.');
            return;
        }

        // Crédito
        $card->balance += $fee;
        $card->save();

        Operation::create([
            'card_id' => $card->id,
            'type' => 'credit',
            'value' => $fee,
            'date' => now(),
            'credit_type' => 'payment',
            'payment_type' => $this->payment_type,
            'payment_reference' => $this->payment_reference,
        ]);

        // Débito
        $card->balance -= $fee;
        $card->save();

        Operation::create([
            'card_id' => $card->id,
            'type' => 'debit',
            'value' => $fee,
            'date' => now(),
            'debit_type' => 'membership_fee',
        ]);

        $user->type = 'member';
        $user->save();

        session()->flash('success', 'Membership confirmed!');
        return redirect()->route('catalog');
    }

    public function render()
    {
        return view('livewire.pages.membership.confirm')->layout('layouts.app');
    }
}
