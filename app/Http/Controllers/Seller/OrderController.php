<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->person->sellerOrders()
            ->with(['buyer', 'items.product'])
            ->latest()
            ->paginate(10);

        return view('seller.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['buyer', 'items.product', 'statusHistory']);

        return view('seller.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:255',
        ]);

        $order->update(['status' => $request->status]);
        
        // Registrar el cambio de estado
        $order->statusHistory()->create([
            'status' => $request->status,
            'notes' => $request->notes,
            'changed_by_id' => auth()->id(),
        ]);

        return back()->with('success', 'Estado de la orden actualizado exitosamente.');
    }
} 