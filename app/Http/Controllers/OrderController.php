<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCompleted;
use App\Models\Operation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use PDF;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;



class OrderController extends Controller
{
    use AuthorizesRequests;

    // club member order details -> for club members
    public function show(Order $order, Request $request)
    {
        $user = Auth::user();

        if ($user->id !== $order->member_id && $user->type !== 'board') {
            abort(403, 'Unauthorized access to order details.');
        }

        $products = $order->products()->paginate(5)->appends($request->query());

        return view('order.details', compact('order', 'products'));
    }

    public function pending()
    {
        $orders = Order::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('order.pending', compact('orders'));
    }

    public function showPendingDetails(Order $order, Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->type, ['employee', 'board']) || $order->status !== 'pending') {
            abort(403);
        }

        $products = $order->products()->paginate(5)->appends($request->query());

        return view('order.pending-details', compact('order', 'products'));
    }


    public function complete(Order $order)
    {
        $this->authorize('complete', $order); 

        // Garante que carregas os produtos da encomenda
        $order->load('products');

        // Verifica stock
        foreach ($order->products as $product) {
            if ($product->stock < $product->pivot->quantity) {
                return redirect()->back()->with('error', 'There are products with insufficient stock. Can not complete order');
            }
        }

        // Se chegou aqui, stock ok, realiza operação dentro de transação
        DB::transaction(function () use ($order) {
            foreach ($order->products as $product) {
                $product->decrement('stock', $product->pivot->quantity);
            }

            // Gera pdf (função já implementada?)
            $receiptPath = $this->generatePdfReceipt($order);

            $order->update([
                'status' => 'completed',
                'pdf_receipt' => $receiptPath,
            ]);

            // Envia email
            Mail::to($order->user->email)->send(new OrderCompleted($order));
        });

        return redirect()->route('order.pending')->with('success', 'Order marked as completed.');
    }

    public function cancel(Request $request, Order $order)
    {
        $this->authorize('cancel', $order); 

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($order, $request) {
            $order->update([
                'status' => 'canceled',
                'cancel_reason' => $request->input('reason'),
            ]);

            $order->user->card->increment('balance', $order->total);
            Operation::create([
                'card_id' => $order->user->card->id,
                'type' => 'credit',
                'credit_type' => 'order_cancellation',
                'value' => $order->total,
                'order_id' => $order->id,
                'date' => now()->toDateString(),
            ]);
        });

        Mail::to($order->user->email)->send(new \App\Mail\OrderCancelled($order));

        return redirect()->route('order.pending')->with('success', 'Order canceled and refunded.');
    }

    protected function generatePdfReceipt(Order $order): string
    {
        // Carrega produtos para garantir que os dados estão prontos
        $order->load('products');

        $products = $order->products;

        // Gera o PDF com a view do recibo
        $pdf = PDF::loadView('order.receipt', compact('order', 'products'));

        // Define o caminho e nome do ficheiro na storage privada
        $filename = 'receipts/receipt-order-' . $order->id . '-' . now()->format('YmdHis') . '.pdf';

        // Guarda o PDF na storage/app/private
        Storage::disk('private')->put($filename, $pdf->output());

        // Retorna o caminho relativo para guardar no BD (se quiseres guardar na encomenda)
        return $filename;
    }

    public function downloadReceipt(Order $order)
    {
        $this->authorize('viewReceipt', $order);

        if (!$order->pdf_receipt || !Storage::disk('private')->exists($order->pdf_receipt)) {
            abort(404, 'Receipt not found.');
        }

        return Storage::disk('private')->download($order->pdf_receipt, 'receipt-order-' . $order->id . '.pdf');
    }
}
