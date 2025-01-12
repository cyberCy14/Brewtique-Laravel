<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'total_price',
        // add any other columns you have in the orders table
    ];

    // public function getAllOrders()
    // {
    //     // Retrieve all orders from the database
    //     $orders = Order::all();

    //     // Return orders as JSON (or pass to a view if you're rendering a page)
    //     return response()->json($orders);
    // }

    /**
     * An Order can have many OrderItems.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * An Order belongs to one User (the buyer).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
