<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::with(['user.role', 'attendances'])->latest('joining_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('staff_code', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('designation', 'like', "%{$s}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $staff = $query->paginate(12)->withQueryString();

        $totalPayroll = Staff::where('status', 'Active')->sum('salary');
        $totalActive = Staff::where('status', 'Active')->count();

        return view('staff.index', compact('staff', 'totalPayroll', 'totalActive'));
    }

    public function create()
    {
        $roles = Role::all();
        $lastStaff = Staff::latest('id')->first();
        $nextNum = $lastStaff ? ($lastStaff->id + 1) : 1;
        $suggestedCode = sprintf('STF-2026-%03d', $nextNum);

        return view('staff.create', compact('roles', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_code' => 'required|string|max:50|unique:staff,staff_code',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:staff,email',
            'mobile' => 'required|string|max:20',
            'designation' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'joining_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'address' => 'nullable|string',
            'status' => 'required|in:Active,Inactive,On Leave',
            'role_id' => 'nullable|exists:roles,id',
            'create_user_account' => 'nullable|boolean',
        ]);

        return DB::transaction(function() use ($validated, $request) {
            $userId = null;

            if ($request->has('create_user_account')) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make('password'),
                    'role_id' => $validated['role_id'] ?? 7, // Staff role
                    'phone' => $validated['mobile'],
                    'status' => 'Active',
                ]);
                $userId = $user->id;
            }

            $staff = Staff::create([
                'staff_code' => $validated['staff_code'],
                'user_id' => $userId,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'designation' => $validated['designation'],
                'department' => $validated['department'],
                'joining_date' => $validated['joining_date'],
                'salary' => $validated['salary'],
                'address' => $validated['address'] ?? null,
                'status' => $validated['status'],
            ]);

            ActivityLog::record(
                'Create Staff',
                'Staff',
                $staff->id,
                "Registered staff member: {$staff->name} ({$staff->staff_code}) in {$staff->department}."
            );

            return redirect()->route('staff.index')->with('success', "Staff {$staff->name} added successfully.");
        });
    }

    public function show($id)
    {
        $staffMember = Staff::with(['user.role', 'attendances', 'leaves'])->findOrFail($id);
        return view('staff.show', compact('staffMember'));
    }

    public function edit($id)
    {
        $staffMember = Staff::with('user')->findOrFail($id);
        $roles = Role::all();
        return view('staff.edit', compact('staffMember', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:staff,email,' . $staff->id,
            'mobile' => 'required|string|max:20',
            'designation' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'joining_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'address' => 'nullable|string',
            'status' => 'required|in:Active,Inactive,On Leave',
        ]);

        $staff->update($validated);

        ActivityLog::record('Update Staff', 'Staff', $staff->id, "Updated staff profile {$staff->name}.");

        return redirect()->route('staff.index')->with('success', 'Staff details updated.');
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $name = $staff->name;
        $staff->delete();

        ActivityLog::record('Delete Staff', 'Staff', $id, "Deleted staff {$name}.");

        return redirect()->route('staff.index')->with('success', "Staff {$name} removed.");
    }
}
