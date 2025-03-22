<?php
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Companies\Warehouse;
use App\Models\Companies\Company;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseController extends Controller{

    // public function index() : Response
    //  {
    //     $warehouses = Warehouse::latest()->get();

    //     return Inertia::render('Supplier/SupplierWarehouses', [
    //         'warehouses' => $warehouses
    //     ]);
    //  }

     public function index()
     {
        return Inertia::render('Supplier/SupplierWarehouses');
     }

     public function api_store_warehouse(Request $request)
     {
    
         $validated = $request->validate([
             'name' => 'required|string|max:255',
            //  'location' => 'required|string|max:255',
             'contact_person' => 'required|string|max:255',
             'phone_number' => 'required|string|max:20',
             'address' =>'required|string|max:255',
             'email' =>'required|string|email|max:255|unique:warehouses',
             'krapin' =>'required|string|max:10|unique:warehouses',
             'company_id' =>'required|integer|exists:companies,id',
             'created_by' => 'required|integer|exists:users,id'
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
        $warehouses = Warehouse::where('company_id', $company->id)->paginate($request->pageSize);
        return response()->json($warehouses);
    }

    public function update(Request $request)
    {
        try {
            $warehouse = Warehouse::where('uuid', $request->uuid)->firstOrFail();
            // Check if user has permission to update this warehouse
        // if ($warehouse->company_id !== auth()->user()->company_id) {
        //     return response()->json([
        //         'message' => 'Unauthorized action.'
        //     ], 403);
        // }

        $warehouse->update([
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'krapin' => $request->krapin,
            'country' => $request->country,
            'region' => $request->region,
            'gps' => $request->gps,
            'status' => $request->status,
            // 'updated_by' => auth()->id()
        ]);

        return redirect()->back();
        
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating warehouse',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}