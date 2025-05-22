<?php

namespace App\Services;

class Payment
{
    public static function payWithVisa($cardNumber, $cvc)
    {
        // Simula pagamento com cartão Visa
        return $cardNumber === '4242424242424242' && $cvc === '123';
    }

    public static function payWithPayPal($email)
    {
        // Simula pagamento com PayPal
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function payWithMBway($number)
    {
        // Simula pagamento com MB WAY
        return preg_match('/^9\d{8}$/', $number);
    }
}
