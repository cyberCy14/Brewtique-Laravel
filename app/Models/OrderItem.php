<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'coffee_id',
        'quantity',
        'price',
        // add any other columns you have in the order_items table
    ];

    /**
     * An OrderItem belongs to one Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * An OrderItem is for one Coffee product.
     */
    public function coffee()
    {
        return $this->belongsTo(Coffee::class);
    }
}
