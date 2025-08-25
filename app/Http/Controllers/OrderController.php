<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'table', 'products'])->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
            'payment_method' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            $items = [];

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;
                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];
            }

            $order = Order::create([
                'user_id' => $validated['user_id'],
                'table_id' => $validated['table_id'],
                'total_amount' => $totalAmount,
                'final_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
            ]);

            $order->products()->sync($items);
        });

        return response()->json(['message' => 'Order created successfully'], 201);
    }

    public function update(Request $request, Order $order)
    {
        // Viết logic để cập nhật đơn hàng, ví dụ: thêm/bớt sản phẩm
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(null, 204);
    }
}
