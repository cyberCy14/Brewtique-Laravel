<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    /**
     * Display a listing of order items (e.g., for a specific order).
     */
    public function index(Request $request)
    {
        // If you want to fetch items for a specific order, pass it as a query param
        $orderId = $request->query('order_id');
        $order = Order::where('id', $orderId)->where('user_id', $request->user()->id)->firstOrFail();

        $items = $order->items()->with('coffee')->get();
        return response()->json($items);

    }

    /**
     * Show the form for creating a new order item.
     */
    public function create()
    {
        // For web forms: return view('order_items.create');
    }

    /**
     * Store a newly created order item in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id'  => 'required|exists:orders,id',
            'coffee_id' => 'required|exists:coffees,id',
            'quantity'  => 'required|integer|min:1',
            'price'     => 'required|numeric|min:0',
        ]);

        // Ensure the user owns this order
        $order = Order::where('id', $request->order_id)
                      ->where('user_id', $request->user()->id)
                      ->firstOrFail();

        $orderItem = OrderItem::create([
            'order_id'  => $order->id,
            'coffee_id' => $request->coffee_id,
            'quantity'  => $request->quantity,
            'price'     => $request->price,
        ]);

        return response()->json($orderItem, 201);
    }

    /**
     * Display the specified order item.
     */
    public function show(OrderItem $orderItem)
    {
        // Check ownership via the order relationship
        if ($orderItem->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $orderItem->load('coffee');
        return response()->json($orderItem);
    }

    /**
     * Show the form for editing the specified order item.
     */
    public function edit(OrderItem $orderItem)
    {
        // return view('order_items.edit', compact('orderItem'));
    }

    /**
     * Update the specified order item in storage.
     */
    public function update(Request $request, OrderItem $orderItem)
    {
        // Authorization
        if ($orderItem->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'quantity' => 'integer|min:1',
            'price'    => 'numeric|min:0',
        ]);

        $orderItem->update($request->only(['quantity', 'price']));

        return response()->json($orderItem);
    }

    /**
     * Remove the specified order item from storage.
     */
    public function destroy(OrderItem $orderItem)
    {
        // Authorization
        if ($orderItem->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $orderItem->delete();

        return response()->json(['message' => 'Order item removed'], 200);
    }
}
