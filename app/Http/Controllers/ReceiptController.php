<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
     public function download(Order $order)
    {
        $user = Auth::user();

        // Permitir apenas ao dono da encomenda ou utilizadores board
        if ($user->id !== $order->member_id && $user->type !== 'board') {
            abort(403, 'You are not authorized to access this receipt.');
        }

        $filePath = 'private/receipts/' . basename($order->pdf_receipt);

        if (!$order->pdf_receipt || !Storage::exists($filePath)) {
            abort(404, 'Receipt not found.');
        }

        return Storage::download($filePath);
    }
}
