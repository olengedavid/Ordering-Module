<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Companies\Warehouse;
use App\Models\Companies\Company;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseController extends Controller
{

    public function index()
    {
        return Inertia::render('Supplier/SupplierWarehouses');
    }

    public function api_store_warehouse(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:warehouses',
            'krapin' => 'required|string|max:10|unique:warehouses',
            'company_id' => 'required|integer|exists:companies,id',
            'created_by' => 'required|integer|exists:users,id',
            'region' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'gps_location' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $gps = json_decode($value, true);
                        if (!isset($gps['latitude']) || !isset($gps['longitude'])) {
                            $fail('GPS location must contain both latitude and longitude.');
                        }
                    }
                }
            ],
        ]);

        Warehouse::create($validated + ['status' => 'active']);

        return redirect()->back();
    }


    public function getWarehousesBySupplier(Request $request)
    {
        $company = Company::where('uuid', $request->uuid)->firstOrFail();
        $warehouses = Warehouse::where('company_id', $company->id)->get();
        return response()->json($warehouses);
    }

    public function getPaginatedSupplierWarehouses(Request $request)
    {
        $company = Company::where('uuid', $request->uuid)->firstOrFail();
        $search = $request->input('search');


        $warehouses = Warehouse::where('company_id', $company->id)
            ->when($search, function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->paginate($request->pageSize);

        return response()->json($warehouses);
    }

    public function update(Request $request)
    {
        try {
            $warehouse = Warehouse::where('uuid', $request->uuid)->firstOrFail();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'contact_person' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'address' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:warehouses,email,' . $warehouse->id,
                'krapin' => 'required|string|max:10|unique:warehouses,krapin,' . $warehouse->id,
                'region' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
                'gps_location' => [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            $gps = json_decode($value, true);
                            if (!isset($gps['latitude']) || !isset($gps['longitude'])) {
                                $fail('GPS location must contain both latitude and longitude.');
                            }
                        }
                    }
                ],
            ]);

            $warehouse->update($validated);

            return redirect()->back()->with('success', 'Warehouse updated successfully');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating warehouse',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($uuid)
    {
        $warehouse = Warehouse::where('uuid', $uuid)->firstOrFail();
        try {
            $warehouse->delete();
            return redirect()->back()
                ->with('success', 'Warehouse deleted successfully.');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting warehouse'], 500);
        }
    }
}
