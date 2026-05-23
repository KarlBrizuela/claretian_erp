<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    private $mainDivisions = [
        'Marketing Division',
        'Production Division',
        'Admin & Finance Division'
    ];

    private $allPositions = [
        'Super Admin',
        'Director',
        'Manager',
        'Senior Accounting Staff',
        'Accounting Staff',
        'Inventory Staff',
        'Cashier',
        'Credit and Collection Staff',
        'Senior Inventory and Collection Staff',
        'Inventory and Collection Staff',
        'Billing Staff',
        'MIS Supervisor',
        'MIS Staff',
        'GSD Supervisor',
        'HR Staff',
        'Senior Supervisor',
        'Account Executive',
        'Marketing Coordinator',
        'Bookstore Staff',
        'Bookschain Staff',
        'Booksales Staff',
        'Marketing Staff',
        'DTO Supervisor',
        'Senior Ford Staff',
        'Ford Staff',
        'Senior Logistics Staff',
        'Logistics Staff',
        'Printing Services Staff'
    ];

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        
        $currentUser = auth()->user();
        $sidebar = $currentUser->division === 'All Divisions' ? 'director' : 'super-admin';

        return view('super-admin.users', [
            'title' => 'User Management',
            'role' => 'Super Admin',
            'sidebar' => $sidebar,
            'users' => $users,
            'positions' => $this->allPositions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_number' => 'required|string|unique:users,employee_number',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'division' => 'required|string',
            'department' => 'nullable|string',
            'position' => 'required|string',
            'status' => 'boolean',
        ]);

        $validated['plain_password'] = $validated['password'];
        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $request->has('status') ? true : false;
        
        $user = User::create($validated);
        
        // Also add to division_user table for access control
        // Auto-assign all three divisions for Director accounts
        if ($user->position === 'Director') {
            foreach ($this->mainDivisions as $div) {
                \App\Models\UserDivision::create([
                    'user_id' => $user->id,
                    'division' => $div
                ]);
            }
        } else {
            \App\Models\UserDivision::create([
                'user_id' => $user->id,
                'division' => $user->division
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'employee_number' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'division' => 'required|string',
            'department' => 'nullable|string',
            'position' => 'required|string',
            'status' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['plain_password'] = $validated['password'];
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['status'] = $request->has('status') ? true : false;

        $user->update($validated);

        // Auto-sync divisions if updated to Director
        if ($user->position === 'Director') {
            $user->divisions()->delete();
            foreach ($this->mainDivisions as $div) {
                \App\Models\UserDivision::create([
                    'user_id' => $user->id,
                    'division' => $div
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    public function roles()
    {
        // 1. Auto-Sync: Check for positions in Users table that don't exist in Roles table
        $distinctUserRoles = User::distinct()->pluck('position');
        
        foreach ($distinctUserRoles as $userRoleName) {
            if ($userRoleName && !\App\Models\Role::where('name', $userRoleName)->exists()) {
                
                // Find a user with this position to get their division
                $sampleUser = User::where('position', $userRoleName)->first();
                $division = $sampleUser ? $sampleUser->division : 'All Divisions';

                // Create the missing role automatically
                \App\Models\Role::create([
                    'name' => $userRoleName,
                    'division' => $division, // Use the user's division
                    'permission_level' => 'VIEW', // Default safe permission
                    'access_scope' => 'OWN_DEPARTMENT', // Default safe scope
                    'description' => 'Automatically created from user assignment',
                    'is_active' => true
                ]);
            }
        }

        // Also ensure all master positions exist as roles
        foreach ($this->allPositions as $posName) {
            if (!\App\Models\Role::where('name', $posName)->exists()) {
                \App\Models\Role::create([
                    'name' => $posName,
                    'is_active' => true,
                    'description' => 'Master Position Role'
                ]);
            }
        }

        // 1b. Ensure all users have their 'position' column set if empty (usually not needed if created via UI)
        User::whereNull('position')->orWhere('position', '')->each(function ($user) {
            // Find a valid position or leave as N/A
        });

        // 2. Fetch users and filtered roles
        $users = User::with('divisions')->orderBy('created_at', 'desc')->get();
        $roles = \App\Models\Role::whereIn('name', $this->allPositions)
            ->orderBy('name', 'asc')
            ->get();

        // Define all available permission keys for the UI
        $allAvailablePermissions = [
            'Marketing' => [
                'marketing.dashboard' => 'Dashboard',
                'marketing.customers' => 'Customer Management',
                'marketing.area_sales' => 'Area Sales (Sales Orders, AR, Memo)',
                'marketing.direct_sales' => 'Direct Sales (POS, NBS)',
                'marketing.ads_promo' => 'Ads & Promo / Marketing Plan',
                'marketing.ecom' => 'E-Commerce Tabs',
                'marketing.book_mgmt' => 'Book Management (Master)',
                'marketing.supplier_mgmt' => 'Supplier Management',
                'marketing.approval_queue' => 'Approval Queue',
                'marketing.my_requests' => 'My Requests',
            ],
            'Production' => [
                'production.dashboard' => 'Dashboard',
                'production.inventory' => 'Inventory Management',
                'production.logistic' => 'Logistics (Delivery, PO, RR)',
                'production.dto' => 'DTO (Job Orders)',
                'production.ford' => 'FORD (Payment Posting, RFQ)',
                'production.printing' => 'Printing Services',
                'production.approval_queue' => 'Approval Queue',
                'production.my_requests' => 'My Requests',
            ],
            'Admin & Finance' => [
                'admin_finance.dashboard' => 'Dashboard',
                'admin_finance.mis' => 'MIS (Job Orders, CCTV, QB)',
                'admin_finance.gsd' => 'GSD (Asset, Job Orders)',
                'admin_finance.credit_collection' => 'Credit & Collection (Billing, SOA)',
                'admin_finance.accounting' => 'Accounting (Invoice, Petty Cash, Journal)',
                'admin_finance.expense_management' => 'Expense Management',
                'admin_finance.hr' => 'Human Resources',
                'admin_finance.approval_queue' => 'Approval Queue',
                'admin_finance.my_requests' => 'My Requests',
            ]
        ];

        $currentUser = auth()->user();
        $sidebar = $currentUser->division === 'All Divisions' ? 'director' : 'super-admin';

        // Build role permissions map for JavaScript
        $rolePermissionsMap = [];
        foreach ($roles as $roleItem) {
            $rolePermissionsMap[$roleItem->name] = $roleItem->permissions ?? [];
        }

        return view('super-admin.roles', [
            'title' => 'Roles & Permissions',
            'role' => 'Super Admin',
            'sidebar' => $sidebar,
            'users' => $users,
            'roles' => $roles,
            'positions' => $this->allPositions,
            'availablePermissions' => $allAvailablePermissions,
            'rolePermissionsMap' => $rolePermissionsMap
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // 1. Sync Divisions
        // Remove existing divisions
        $user->divisions()->delete();

        // Add selected divisions
        $divisions = $request->input('divisions', []);
        foreach ($divisions as $division) {
            \App\Models\UserDivision::create([
                'user_id' => $user->id,
                'division' => $division
            ]);
        }
        
        // Update main division field to the first selected one (for backward compatibility)
        if (!empty($divisions)) {
            $user->division = $divisions[0];
        }

        // Update position if provided
        if ($request->has('position')) {
            $user->position = $request->input('position');
        }

        // Update User-Level Permissions (Manual Override)
        // If no permissions are explicitly set, use NULL to fall back to role permissions
        $permissions = $request->input('permissions', []);
        $user->permissions = empty($permissions) ? null : $permissions;
        
        $user->save();

        return redirect()->route('roles.index')->with('success', 'User access updated successfully.');
    }

    public function updateRolePermissions(Request $request, $id)
    {
        $role = \App\Models\Role::findOrFail($id);
        
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $role->update([
            'permissions' => $request->input('permissions', []),
            'description' => $request->input('description'),
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('roles.index')->with('success', 'Role permissions updated successfully.');
    }

    public function divisions()
    {
        $currentUser = auth()->user();
        $sidebar = $currentUser->division === 'All Divisions' ? 'director' : 'super-admin';

        return view('super-admin.divisions', [
            'title' => 'Divisions',
            'role' => 'Super Admin',
            'sidebar' => $sidebar
        ]);
    }

    public function settings()
    {
        $currentUser = auth()->user();
        $sidebar = $currentUser->division === 'All Divisions' ? 'director' : 'super-admin';

        // Load payment settings
        $paymentSettings = \App\Models\PaymentSetting::getAllSettings();

        return view('super-admin.settings', [
            'title' => 'System Settings',
            'role' => 'Super Admin',
            'sidebar' => $sidebar,
            'paymentSettings' => $paymentSettings
        ]);
    }
}
