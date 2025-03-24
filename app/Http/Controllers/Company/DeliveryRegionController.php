<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Companies\DeliveryRegion;

class DeliveryRegionController extends Controller
{
    public function getDeliveryRegionsByCompany(Request $request)
    {
        $regions = DeliveryRegion::where('company_id', $request->company_id)
            ->with(['creator'])
            ->get();
        return response()->json($regions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'region' => 'required|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'company_id' => 'required|exists:companies,id',
            'created_by' => 'required|exists:users,id',
        ]);

        DeliveryRegion::create($validated);
        return redirect()->back()->with('success', 'Delivery region created successfully');
    }

    public function updateDeliveryRegions(Request $request, $uuid)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'region' => 'required|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'company_id' => 'required|exists:companies,id'
        ]);

        try {
            $deliveryRegion = DeliveryRegion::where('uuid', $uuid)->firstOrFail();
            $deliveryRegion->update($validated);

            return redirect()->back()
                ->with('success', 'Delivery region updated successfully.');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update delivery region',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($uuid)
    {
        $deliveryRegion = DeliveryRegion::where('uuid', $uuid)->firstOrFail();

        try {
            $deliveryRegion->delete();
            return response()->json(['message' => 'Delivery region deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting delivery region'], 500);
        }
    }
}
