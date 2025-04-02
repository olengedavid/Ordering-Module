<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Companies\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate(Order::validationRules());
        
        $order = Order::create($validated);
        
        return response()->json($order, 201);
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