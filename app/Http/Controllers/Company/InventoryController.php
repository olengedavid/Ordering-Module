<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Companies\Company;
use App\Models\Companies\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class InventoryController extends Controller
{

    public function index()
    {
        return Inertia::render('Supplier/InventoryList');
    }

    public function getInventoriesBySupplier(Request $request)
    {
        $company = Company::where('uuid', $request->uuid)->firstOrFail();

        $inventories = Inventory::where('company_id', $company->id)
            ->with(['product', 'warehouse'])
            ->latest()
            ->paginate($request->per_page);

        return response()->json($inventories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
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
        ]);

        Inventory::create($validated);

        return redirect()->back()
        ->with('success', 'Inventory created successfully.');
    }

    public function update(Request $request, Inventory $inventory)
    {
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

        return response()->json($inventory);
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return response()->json(null, 204);
    }
}
