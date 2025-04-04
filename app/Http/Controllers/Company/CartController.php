<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Companies\CartItem;
use App\Models\Companies\Inventory;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validated = $request->validate(CartItem::validationRules());
        
        $inventory = Inventory::findOrFail($validated['warehouse_inventory_id']);
        
        try {
            $cartItem = CartItem::create([
                ...$validated,
                'total_price' => $validated['quantity'] * $validated['unit_price']
            ]);

            return response()->json([
                'message' => 'Item added to cart successfully',
                'cart_item' => $cartItem->load('inventory')
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'message' => 'This item is already in your cart. Please update the quantity instead of adding it again.',
                    'type' => 'duplicate_item'
                ], 422);
            }

            return response()->json([
                'message' => 'Failed to add item to cart',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error adding item to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeFromCart($uuid)
    {
        try {
            $cartItem = CartItem::where('uuid', $uuid)->firstOrFail();
            $cartItem->delete();

            return response()->json([
                'message' => 'Item removed from cart successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error removing item from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCartItems(Request $request)
    {
        $cartItems = CartItem::where('retailer_id', $request->retailer_id)
            ->with(['inventory.product', 'supplier:id,company_name'])
            ->get();

        return response()->json($cartItems);
    }

    public function clearCart(Request $request)
    {
        $query = CartItem::where('retailer_id', $request->retailer_id);
        
        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        $query->delete();
        
        return response()->json([
            'message' => 'Cart items cleared successfully'
        ]);
    }
}