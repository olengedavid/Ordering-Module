<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\userPermission;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Companies\Company;
use Illuminate\Support\Facades\Log;

class UserPermissionController extends BaseController
{

    public function index()
    {
        return Inertia::render('Supplier/SupplierUsers');
    }

    protected $casts = [
        'uuid' => 'string',
        'permissions' => 'array'
    ];

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'permissions' => ['array', 'distinct', 'required'],
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'company_id' => 'nullable|exists:companies,id',
            'status' => 'required|in:active,inactive',
            'entity_type' => 'required|in:supplier,admin',
            'retailer',
            'created_by' => 'required|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Create the user
            $user = User::create([
                'name' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => $validated['status'],
                'company_id' => $validated['company_id'],
            ]);

            // echo "User created successfully. ++";

            // Create user permissions
            userPermission::create([
                'user_id' => $user->id,
                'permissions' => json_encode($validated['permissions']),
                'warehouse_id' => $validated['warehouse_id'],
                'entity_type' => $validated['entity_type'],
                'created_by' => $validated['created_by'],
                'updated_by' => $validated['updated_by'],
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'User and permissions created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User creation failed: ' . $e->getMessage());  // Add logging
            return redirect()->back()
            ->with('error', 'Failed to create user and permissions: ' . $e->getMessage());
        }
    }

    public function getSupplierUsers(Request $request)
    {
        $company = Company::where('uuid', $request->uuid)->firstOrFail();
        
        $users = User::where('company_id', $company->id)
            ->with(['userPermissions' => function($query) {
                $query->select('id', 'user_id', 'permissions', 'warehouse_id', 'entity_type')
                    ->with(['warehouse' => function($q) {
                        $q->select('id', 'name', 'uuid');
                    }]);
            }])
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'email', 'company_id')
            ->paginate($request->perPage ?? 10);
    
        return response()->json($users);
    }
}
