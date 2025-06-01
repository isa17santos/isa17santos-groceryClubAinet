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

        // 1. Caminho como está guardado na base de dados
        $originalPath = $order->pdf_receipt;

        // 2. Caminho alternativo, assumindo que faltava o prefixo 'receipts/'
        $fallbackPath = 'receipts/' . basename($order->pdf_receipt);

        // Tenta primeiro o caminho original
        if (Storage::disk('private')->exists($originalPath)) {
            return Storage::disk('private')->download($originalPath);
        }

        // Depois tenta o fallback
        if (Storage::disk('private')->exists($fallbackPath)) {
            return Storage::disk('private')->download($fallbackPath);
        }

        if (!$order->pdf_receipt || !Storage::exists($filePath)) {
            abort(404, 'Receipt not found.');
        }

        return Storage::download($filePath);
    }
}
