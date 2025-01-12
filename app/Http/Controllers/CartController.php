<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coffee;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CartController extends Controller
{
    use AuthorizesRequests;
    
    // Get Cart Items
    public function index(Request $request)
    {
        $user = $request->user();
    
        // Fetch cart items with associated coffee
        $cartItems = $user->cart()->with('coffee')->get();
    
        // Map over the cart items to modify the coffee image URL
        $cartItems->map(function ($cartItem) {
            if ($cartItem->coffee) {
                $cartItem->coffee->img = asset('images/coffeeOptionsImages/' . $cartItem->coffee->img);
            }
            return $cartItem;

        });
        

        return response()->json($cartItems);
        Console::info('mymessage' + json($cartItems)); 
    }


    // Add to Cart
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'coffee_id' => 'required|exists:coffees,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::updateOrCreate(
            ['user_id' => $request->user()->id, 'coffee_id' => $validated['coffee_id']],
            ['quantity' => $validated['quantity']]
        );

        return response()->json(['message' => 'Item added to cart', 'cart' => $cart]);
    }


    // Update Cart Item
    public function updateCart(Request $request, Cart $cart)
    {
        $this->authorize('update', $cart);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart->update(['quantity' => $validated['quantity']]);
        return response()->json(['message' => 'Cart updated', 'cart' => $cart]);
    }


    // Remove from Cart
    public function deleteCart(Cart $cart)
    {
        // Ensure the cart item belongs to the authenticated user
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cart->delete();

        return response()->json(['message' => 'Item removed from cart'], 200);
    }

    // Checkout
    public function checkout(Request $request)
    {
        $user = $request->user();
    
        // Fetch cart items
        $cartItems = Cart::where('user_id', $user->id)
            ->with('coffee')
            ->get();
    
        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'No items in cart'], 400);
        }
    
        // Calculate total price
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += $item->coffee->price * $item->quantity;
        }
    
        // Create Order
        $order = Order::create([
            'user_id'      => $user->id,
            'order_number' => strtoupper(uniqid('ORDER-')),
            'total_price'  => $totalPrice
        ]);
    
        // Create Order Items
        foreach ($cartItems as $item) {
            $order->items()->create([
                'coffee_id' => $item->coffee_id,
                'quantity'  => $item->quantity,
                'price'     => $item->coffee->price,
            ]);
        }
    
        // Clear cart
        Cart::where('user_id', $user->id)->delete();
    
        return response()->json([
            'message'      => 'Order placed successfully!',
            'order_id'     => $order->id,
            'order_number' => $order->order_number
        ]);
    }
    
    
}

