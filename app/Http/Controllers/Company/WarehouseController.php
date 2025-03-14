<?php
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Companies\Warehouse;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseController extends Controller{

    public function index() : Response
     {
        $warehouses = Warehouse::latest()->get();

        return Inertia::render('Supplier/SupplierWarehouses', [
            'warehouses' => $warehouses
        ]);
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

         ]);
     
         Warehouse::create($validated + ['status' => 'active']);
     
         return redirect()->back();
     }

}