<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $product = Product::find($request->product_id);
        if (!$product) {
            Log::error('product not found. Product ID: ' . $request->Product_id);
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        if ($product->Available_stock < $request->quantity) {
            return response()->json([
                'message' => 'Not enough stock available',
            ], 400);
        }

        $product->Available_stock = $product->Available_stock - $request->quantity;
        $product->save();

        $order = new Order;
        $order->product_id = $product->id;
        $order->quantity = $request->quantity;
        $order->save();

        return response()->json([
            'message' => 'Order placed successfully',
            'data' => [
                'order_id' => $order->id,
            ],
        ], 201);
    }
    
    }





    // public function store(Request $request, Product $product){
    //     $request->validate([
    //         'quantity' => 'required|integer|min:1'
    //     ]);

    //     if ($request->quantity >= $product->available_stock){
    //         return response()->json([
    //             'error' => 'Failed to order this product due to unavailability of the stock'
    //         ],400);
    //     }

    //     $product = Order::create([
    //         'product_id' => $product->id,
    //         'quantity' => $request->quantity,
    //     ]);

    //     $product->update(['Available_stock' => $product->Available_stock - $request->quantity]);

    //     return response()->json([
    //         'message' => 'You have successfully ordered this product'
    //     ], 201);
    // }
    

