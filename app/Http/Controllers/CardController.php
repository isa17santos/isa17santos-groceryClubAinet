<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Payment;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CardController extends Controller
{
    use AuthorizesRequests;

    public function show(Request $request)
    {
        $this->authorize('access-card');

        $card = Auth::user()->card;
        $operations = Operation::where('card_id', $card->id)
                        ->orderByDesc('date')
                        ->paginate(5)
                        ->appends($request->query());

        return view('card.index', compact('card', 'operations'));
    }

    public function credit(Request $request)
    {
        $this->authorize('access-card');

        $request->validate([
            'payment_type' => 'required|in:Visa,PayPal,MB WAY',
            'payment_reference' => 'required|string',
            'amount' => 'required|numeric|min:1'
        ]);

        $paymentType = $request->input('payment_type');
        $reference = $request->input('payment_reference');
        $amount = $request->input('amount');
        $success = false;

        if ($paymentType === 'Visa') {
            $success = Payment::payWithVisa($reference, substr($reference, -3));
        } elseif ($paymentType === 'PayPal') {
            $success = Payment::payWithPayPal($reference);
        } elseif ($paymentType === 'MB WAY') {
            $success = Payment::payWithMBway($reference);
        }

        if (!$success) {
            return back()->with('error', 'Payment simulation failed.');
        }

        $card = Auth::user()->card;
        $card->balance += $amount;
        $card->save();

        Operation::create([
            'card_id' => $card->id,
            'type' => 'credit',
            'value' => $amount,
            'date' => now()->toDateString(),
            'credit_type' => 'payment',
            'payment_type' => $paymentType,
            'payment_reference' => $reference
        ]);

        return back()->with('success', 'Card credited successfully!');
    }
}
