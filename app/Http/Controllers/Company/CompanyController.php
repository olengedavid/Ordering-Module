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
            'industry' => 'required|string|max:255',
        ]);

        Company::create([
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'office_address' => $request->office_address,
            'registration_number' => $request->registration_number,
            'krapin' => $request->krapin,
            'contact_person' => $request->contact_person,
            'industry' => $request->industry,
            'created_by' => $request->created_by
        ]);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'New Company has been created!');
    }

    /**
     * Update the specified company.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'uuid' => 'required|exists:companies,uuid',
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone_number' => 'required|string|max:20',
            'office_address' => 'required|string|max:255',
            'krapin' => 'required|string|max:10',
            'contact_person' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
        ]);

        $company = Company::where('uuid', $request->uuid)->firstOrFail();
        
        // Check if email is unique (except for this company)
        if ($company->email !== $request->email) {
            $request->validate([
                'email' => 'unique:companies,email'
            ]);
        }
        
        // Check if krapin is unique (except for this company)
        if ($company->krapin !== $request->krapin) {
            $request->validate([
                'krapin' => 'unique:companies,krapin'
            ]);
        }

        $company->update([
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'office_address' => $request->office_address,
            'krapin' => $request->krapin,
            'contact_person' => $request->contact_person,
            'industry' => $request->industry,
        ]);

        return redirect()->back()->with('success', 'Company information updated successfully');
    }

    /**
     * Activate the specified company.
     */
    public function activate(Request $request): RedirectResponse
    {
        $request->validate([
            'uuid' => 'required|exists:companies,uuid',
            'status' => 'required|string|in:active'
        ]);

        $company = Company::where('uuid', $request->uuid)->firstOrFail();
        $company->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Company activated successfully');
    }
}
