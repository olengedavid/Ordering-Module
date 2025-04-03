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

    public function update(Request $request, $uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();
        
        $rules = Order::validationRules();
        $rules = array_map(function($rule) {
            return str_replace('required|', 'sometimes|', $rule);
        }, $rules);

        if ($request->has('products')) {
            $request->validate([
                'products' => 'array|min:1',
                'products.*.warehouse_inventory_id' => 'required|exists:inventories,id',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.unit_price' => 'required|numeric|min:0',
            ]);
        }

        try {
            \DB::beginTransaction();

            $order->update($validated);

            if ($request->has('products')) {
                $order->orderedProducts()->delete();

                foreach ($request->products as $product) {
                    $order->orderedProducts()->create([
                        'warehouse_inventory_id' => $product['warehouse_inventory_id'],
                        'quantity' => $product['quantity'],
                        'unit_price' => $product['unit_price'],
                        'total_price' => $product['quantity'] * $product['unit_price'],
                        'created_by' => $request->created_by
                    ]);
                }
            }

            \DB::commit();

            return response()->json([
                'message' => 'Order updated successfully',
                'order' => $order->load('orderedProducts')
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 'Error updating order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}