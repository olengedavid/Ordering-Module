<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Companies\Company;
use App\Models\Companies\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{

    public function index()
    {
        return Inertia::render('Supplier/InventoryList');
    }

    public function retailersIndex()
    {
        return Inertia::render('Retail/ProductListing');
    }

    public function getRetailerProducts(Request $request)
    {
        $query = Inventory::query()
            ->select(
                'inventories.id as inventory_id',
                'products.*',
                'inventories.stock_quantity',
                'inventories.selling_price',
                'inventories.min_order',
                'inventories.max_order',
                'inventories.currency',
                'inventories.quantity_per_unit',
                'companies.company_name as supplier',
                'inventories.company_id',
                'inventories.warehouse_id',
            )
            ->join('companies', 'inventories.company_id', '=', 'companies.id')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->join('warehouses', 'inventories.warehouse_id', '=', 'warehouses.id')
            ->join('delivery_regions', 'warehouses.id', '=', 'delivery_regions.warehouse_id')
            ->where('products.status', 'active')
            ->where('inventories.stock_quantity', '>', 0);

        if ($request->search) {
            $query->where('products.name', 'like', '%' . $request->search . '%');
        }

        if ($request->region) {
            $query->where('delivery_regions.region', $request->region);
        }

        if ($request->category) {
            $query->where('products.category', $request->category);
        }

        if ($request->manufacturer) {
            $query->where('products.manufucturer', $request->manufacturer);
        }

        if ($request->lastId) {
            $query->where('inventories.id', '>', $request->lastId);
        }

        $limit = $request->limit ?? 20;
        return $query->orderBy('inventories.id')
                    ->limit($limit)
                    ->get();
    }

    public function getInventoriesBySupplier(Request $request)
    {
        $company = Company::where('uuid', $request->uuid)->firstOrFail();
        $search = $request->input('search');

        $inventories = Inventory::where('company_id', $company->id)
            ->with(['product', 'warehouse'])
            ->whereHas('product', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('warehouse', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($request->per_page);

        return response()->json($inventories);
    }

    public function store(Request $request)
    {

        $messages = [
            'warehouse_id.unique' => 'This product already exists in the selected warehouse. Please update the existing inventory instead.'
        ];

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => [
                'required',
                'exists:warehouses,id',
                Rule::unique('inventories')->where(function ($query) use ($request) {
                    return $query->where('product_id', $request->product_id)
                        ->where('warehouse_id', $request->warehouse_id);
                })
            ],
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'min_order' => 'required|numeric|min:0',
            'max_order' => 'required|numeric|min:0',
            'batch_number' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'promo_amount' => 'nullable|numeric|min:0',
            'promo_start_date' => 'nullable|date',
            'promo_end_date' => 'nullable|date',
            'created_by' => 'required|exists:users,id',
        ], $messages);

        Inventory::create($validated);

        return redirect()->back()
            ->with('success', 'Inventory created successfully.');
    }

    public function update(Request $request)
    {
        try {
            $inventory = Inventory::where('uuid', $request->uuid)->firstOrFail();

            $validated = $request->validate([
                'product_id' => 'sometimes|exists:products,id',
                'warehouse_id' => 'sometimes|exists:warehouses,id',
                'cost_price' => 'sometimes|numeric|min:0',
                'selling_price' => 'sometimes|numeric|min:0',
                'stock_quantity' => 'sometimes|numeric|min:0',
                'min_order' => 'sometimes|numeric|min:0',
                'max_order' => 'sometimes|numeric|min:0',
                'batch_number' => 'nullable|string',
                'expiry_date' => 'nullable|date',
                'promo_amount' => 'nullable|numeric|min:0',
                'promo_start_date' => 'nullable|date',
                'promo_end_date' => 'nullable|date',
                'status' => 'sometimes|in:active,inactive',
            ]);

            $inventory->update($validated);

            return redirect()->back()
                ->with('success', 'Inventory updated successfully.');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating warehouse',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $uuid)
    {
        try {
            $inventory = Inventory::where('uuid', $request->uuid)->firstOrFail();
            $inventory->delete();

            return response()->json([
                'message' => 'Inventory deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting inventory',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
