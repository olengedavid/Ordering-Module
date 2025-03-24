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
// use Illuminate\Support\Facades\Log;

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
                'warehouse_id' => $validated['warehouse_id'],
                'user_type' => $validated['entity_type'],
            ]);

            // echo "User created successfully. ++";

            // Create user permissions
            userPermission::create([
                'user_id' => $user->id,
                'permissions' => json_encode($validated['permissions']),
                'entity_type' => $validated['entity_type'],
                'created_by' => $validated['created_by'],
                'updated_by' => $validated['updated_by'],
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'User and permissions created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('User creation failed: ' . $e->getMessage());  // Add logging
            return redirect()->back()
                ->with('error', 'Failed to create user and permissions: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
            'permissions' => ['array', 'distinct', 'required'],
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'status' => 'required|in:active,inactive',
            'updated_by' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Update user
            $user = User::findOrFail($id);
            $userData = [
                'name' => $validated['username'],
                'email' => $validated['email'],
                'status' => $validated['status'],
                'warehouse_id' => $validated['warehouse_id'],
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            // Update permissions
            $userPermission = userPermission::where('user_id', $id)->first();
            if ($userPermission) {
                $userPermission->update([
                    'permissions' => json_encode($validated['permissions']),
                    'updated_by' => $validated['updated_by'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user->load('userPermissions')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSupplierUsers(Request $request)
    {
        $company = Company::where('uuid', $request->uuid)->firstOrFail();

        $users = User::where('company_id', $company->id)
            ->with([
                'warehouse' => function ($q) {
                    $q->select('id', 'name', 'status', 'uuid');
                },
                'userPermissions' => function ($query) {
                    $query->select('id', 'user_id', 'permissions', 'entity_type');
                }
            ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'email', 'company_id', 'warehouse_id', 'status')
            ->paginate($request->perPage ?? 10);

        return response()->json($users);
    }

    public function getAdminUsers(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $users = User::with([
            'company' => function ($q) {
                $q->select('id', 'company_name', 'uuid');
            },
            'warehouse' => function ($q) {
                $q->select('id', 'name', 'uuid');
            },
            'userPermissions' => function ($q) {
                $q->select('id', 'permissions', 'entity_type');
            }
        ])
            ->select('id', 'name', 'email', 'status', 'user_type','created_at', 'company_id', 'warehouse_id')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($users);
    }
}
