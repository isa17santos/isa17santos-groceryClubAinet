@component('mail::message')
# Your Order Has Been Completed 🎉

Dear {{ $order->user->name }},

Your order #{{ $order->id }} has been successfully completed.  
You will soon receive your items at:

**{{ $order->delivery_address }}**

@if($order->pdf_receipt)
You can also download your receipt from your member area or right here.
@component('mail::button', ['url' => route('receipt.download', ['order' => $order->id])])
Download Receipt
@endcomponent
@endif

Thanks for shopping with Grocery Club!  
@endcomponent
