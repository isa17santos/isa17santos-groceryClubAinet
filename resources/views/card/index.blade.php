@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">Virtual Card</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
        <meta http-equiv="refresh" content="2">
    @elseif(session('error'))
        <div class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
            {{ session('error') }}
        </div>
        <meta http-equiv="refresh" content="2">
    @endif

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <h2 class="text-2xl font-semibold text-lime-700 dark:text-lime-400 mb-4">Card Details</h2>
        <p><strong>Card Number:</strong> {{ $card->card_number }}</p>
        <p><strong>Cardholder's Name:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Balance:</strong> {{ number_format($card->balance, 2) }}€</p>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-10">
        <h2 class="text-2xl font-semibold text-lime-700 dark:text-lime-400 mb-6">Add Funds</h2>

        <form method="POST" action="{{ route('card.credit') }}" class="space-y-4">
            @csrf

            <div>
                <label for="payment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</label>
                <select name="payment_type" id="payment_type" required
                        class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Select a method</option>
                    <option value="Visa">Visa</option>
                    <option value="PayPal">PayPal</option>
                    <option value="MB WAY">MB WAY</option>
                </select>
            </div>

            <div>
                <label for="payment_reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Reference</label>
                <input type="text" name="payment_reference" id="payment_reference" required
                       class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount (€)</label>
                <input type="number" name="amount" id="amount" step="0.01" min="1" required
                       class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
            </div>

            <button type="submit" class="w-full bg-lime-600 text-white py-2 rounded-md hover:bg-lime-700 transition">
                Credit Card
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold text-lime-700 dark:text-lime-400 mb-4">Transaction History</h2>

        @if($operations->isEmpty())
            <p class="text-gray-500 dark:text-gray-300">No transactions yet.</p>
        @else
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($operations as $op)
                    <li class="py-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-semibold {{ $op->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ucfirst($op->type) }}
                                </span>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $op->date }}
                                </span>
                                @if($op->type === 'credit')
                                    <span class="ml-2 text-gray-500 dark:text-gray-300">
                                        ({{ $op->credit_type }} via {{ $op->payment_type }})
                                    </span>
                                @elseif($op->type === 'debit' && $op->order_id)
                                    @if($op->order->pdf_receipt)
                                        <a href="{{ route('receipt.downloadReceipt', $op->order_id) }}" target="_blank" class="ml-2 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                            📄 Receipt
                                        </a>
                                    @endif

                                    <a href="{{ route('orders.show', $op->order_id) }}" class="ml-2 text-sm text-yellow-600 dark:text-yellow-400 hover:underline">
                                        More details
                                    </a>
                                @endif
                            </div>
                            <span class="font-bold">
                                {{ $op->type === 'debit' ? '-' : '+' }}{{ number_format($op->value, 2) }}€
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Paginação -->
            <div class="mt-6 flex justify-center">
                {{ $operations->links('vendor.pagination.tailwind-dark') }}
            </div>
        @endif

    </div>
</div>
@endsection
