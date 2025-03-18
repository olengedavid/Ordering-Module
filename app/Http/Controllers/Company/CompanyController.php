<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\Companies\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class CompanyController extends Controller
{
    /**
     *  Display create supplier page
     */
    public function show(Request $request): Response
    {
        $validated = $request->validate([
            'uuid' => 'required|exists:companies,uuid'
        ]);
    
        $supplier = Company::where('uuid', $validated['uuid'])
            ->with(['bankAccounts'])
            ->firstOrFail();
        
        return Inertia::render('Supplier/SupplierDetails', [
            'supplier' => $supplier,
            'bankAccounts' => $supplier->bankAccounts
        ]);
    }

    public function index(): Response
    {
        $suppliers = Company::latest()->get();

        return Inertia::render('Supplier/Index', [
            'suppliers' => $suppliers
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Supplier/Create');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function api_store_company(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
            'phone_number' => 'required|string|max:20',
            'office_address' => 'required|string|max:255',
            'krapin' => 'required|string|max:10|unique:companies',
            'contact_person' => 'required|string|max:255',
            // 'industry' => 'string|max:255',
        ]);

        Company::create([
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'office_address' => $request->office_address,
            'registration_number' => $request->registration_number,
            'krapin' => $request->krapin,
            'contact_person' => $request->contact_person,
            'industry' => "test industry",
            'created_by' => $request->created_by
        ]);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'New Company has been created!');
    }
}
