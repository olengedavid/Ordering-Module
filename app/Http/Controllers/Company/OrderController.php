<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Companies\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $orderValidated = $request->validate(Order::validationRules());
        $request->validate(OrderedProduct::createRules());

        try {
            \DB::beginTransaction();

            $order = Order::create($orderValidated);

            foreach ($request->products as $product) {
                $order->orderedProducts()->create([
                    'warehouse_inventory_id' => $product['warehouse_inventory_id'],
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['unit_price'],
                    'total_price' => $product['quantity'] * $product['unit_price'],
                    'created_by' => $request->created_by
                ]);
            }

            \DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('orderedProducts')
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 'Error creating order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Order $order)
    {
        $rules = Order::validationRules();
        // Make all fields optional for update
        $rules = array_map(function($rule) {
            return str_replace('required|', 'sometimes|', $rule);
        }, $rules);
        
        $validated = $request->validate($rules);
        
        $order->update($validated);
        
        return response()->json($order);
    }
}