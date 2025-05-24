<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function show(Order $order, Request $request)
    {
        $user = Auth::user();

        if ($user->id !== $order->member_id && $user->type !== 'board') {
            abort(403, 'Unauthorized access to order details.');
        }

        $products = $order->products()->paginate(5)->appends($request->query());

        return view('order.details', compact('order', 'products'));
    }
}
