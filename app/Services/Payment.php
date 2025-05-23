<?php

namespace App\Services;

class Payment
{
    public static function payWithVisa($cardNumber, $cvc)
    {
        // Aceita qualquer número de 16 dígitos e CVC de 3 dígitos
        return preg_match('/^\d{16}$/', $cardNumber) && preg_match('/^\d{3}$/', $cvc);
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
