<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])->get();
        $permissionsByModule = Permission::all()->groupBy('module');
        $users = User::with('role')->paginate(10);

        return view('roles.index', compact('roles', 'permissionsByModule', 'users'));
    }

    public function editRolePermissions($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissionsByModule = Permission::all()->groupBy('module');
        return view('roles.edit_permissions', compact('role', 'permissionsByModule'));
    }

    public function updateRolePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->slug === 'super-admin') {
            return back()->with('error', 'Super Admin role always possesses unrestricted system permissions.');
        }

        $permissions = $request->input('permissions', []);
        $role->permissions()->sync($permissions);

        ActivityLog::record(
            'Update Permissions',
            'Roles',
            $role->id,
            "Updated module permissions for role '{$role->name}' (" . count($permissions) . " permissions assigned)."
        );

        return redirect()->route('roles.index')->with('success', "Permissions updated for role '{$role->name}'.");
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'phone' => $request->phone,
            'status' => 'Active',
        ]);

        ActivityLog::record(
            'Create User',
            'Users',
            $user->id,
            "Created portal user account {$user->name} with role {$user->role->name}."
        );

        return back()->with('success', "User account for {$user->name} created successfully.");
    }

    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        $user->update([
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        ActivityLog::record(
            'Update User Role',
            'Users',
            $user->id,
            "Updated user {$user->name} role to {$user->role->name} and status to {$user->status}."
        );

        return back()->with('success', "User {$user->name} role updated.");
    }
}
