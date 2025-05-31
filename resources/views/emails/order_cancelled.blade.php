@php
    $user = $order->user;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Cancelled</title>
</head>
<body style="font-family: sans-serif; background-color: #f5f5f5; padding: 2rem; color: #333;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 2rem; border-radius: 10px;">
        <h2 style="color: #cc0000;">Order Cancelled</h2>
        <p>Hello {{ $user->name }},</p>

        <p>We would like to inform you that your order <strong>#{{ $order->id }}</strong> has been <strong>cancelled</strong>.</p>

        <p><strong>Reason for cancellation:</strong> {{ $order->cancel_reason ?: 'Unknown' }}</p>

        <p>The total amount of <strong>€{{ number_format($order->total, 2) }}</strong> has been refunded to your virtual card.</p>

        <p>If you have any questions, feel free to contact the club.</p>

        <p style="margin-top: 2rem;">Best regards, <br> Grocery Club Team</p>
    </div>
</body>
</html>
