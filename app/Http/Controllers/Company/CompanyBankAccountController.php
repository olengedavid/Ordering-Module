<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Companies\Company;
use App\Models\Companies\CompanyBankAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyBankAccountController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_uuid' => 'required|exists:companies,uuid',
            'bank_name' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'is_primary' => 'boolean',
            'created_by' => 'required'
        ]);
    
        $company = Company::where('uuid', $validated['company_uuid'])->firstOrFail();
    
        if ($validated['is_primary']) {
            CompanyBankAccount::where('company_id', $company->id)
                ->update(['is_primary' => false]);
        }
    
        CompanyBankAccount::create([
            'company_id' => $company->id,
            'bank_name' => $validated['bank_name'],
            'branch' => $validated['branch'],
            'account_name' => $validated['account_name'],
            'account_number' => $validated['account_number'],
            'is_primary' => $validated['is_primary'],
            'created_by' => $validated['created_by']
        ]);
    
        return back()->with('success', 'Bank account added successfully');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'uuid' => 'required|exists:company_bank_accounts,uuid',
            'bank_name' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'is_primary' => 'boolean'
        ]);
    
        $bankAccount = CompanyBankAccount::where('uuid', $validated['uuid'])->firstOrFail();
    
        if ($validated['is_primary']) {
            CompanyBankAccount::where('company_id', $bankAccount->company_id)
                ->where('id', '!=', $bankAccount->id)
                ->update(['is_primary' => false]);
        }
    
        $bankAccount->update([
            'bank_name' => $validated['bank_name'],
            'branch' => $validated['branch'],
            'account_name' => $validated['account_name'],
            'account_number' => $validated['account_number'],
            'is_primary' => $validated['is_primary']
        ]);
    
        return back()->with('success', 'Bank account updated successfully');
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'uuid' => 'required|exists:company_bank_accounts,uuid'
        ]);
    
        $bankAccount = CompanyBankAccount::where('uuid', $validated['uuid'])->firstOrFail();
        $bankAccount->delete();
    
        return back()->with('success', 'Bank account deleted successfully');
    }

    public function getByCompany(Request $request)
    {
        $validated = $request->validate([
            'company_uuid' => 'required|exists:companies,uuid'
        ]);

        $company = Company::where('uuid', $validated['company_uuid'])->firstOrFail();
        $bankAccounts = CompanyBankAccount::where('company_id', $company->id)->get();

        return response()->json([
            'bankAccounts' => $bankAccounts
        ]);
    }
}