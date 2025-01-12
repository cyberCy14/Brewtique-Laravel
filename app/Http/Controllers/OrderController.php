<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Fetch all orders by this user (with order items)
        $orders = Order::where('user_id', $user->id)
            ->with('items.coffee')  // if you have an 'items' relationship on Order
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);

        // // Only fetch orders for the authenticated user
        // $orders = Order::where('user_id', $request->user()->id)
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        // // Each order will include its `order_number` (assuming you have that column in your orders table)
        // return response()->json($orders);
    }

    /**
     * Show the form for creating a new order (if you're using web forms).
     */
    public function create()
    {
        // Typically return a view if it's a web-based form:
        // return view('orders.create');
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Validate incoming data if necessary:
        // $request->validate([...]);

        // Example: create an order
        $order = Order::create([
            'user_id'      => $user->id,
            'order_number' => strtoupper(Str::uuid()),  // or any unique logic
            'total_price'  => 0,  // you might calculate this based on items
        ]);

        return response()->json($order, 201);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Ensure the user is authorized to view this order
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Load relationship if needed
        $order->load('items.coffee');

        return response()->json($order);
    }

    /**
     * Show the form for editing the specified order (if you're using web forms).
     */
    public function edit(Order $order)
    {
        // return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Authorization check
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // $request->validate([...]);

        // Update fields as needed
        // e.g., $order->update([...]);

        return response()->json($order);
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        // Authorization check
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully'], 200);
    }
}
