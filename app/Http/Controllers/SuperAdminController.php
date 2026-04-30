<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\SuperAdminLoginRequest;
use App\Models\Admin;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function login(SuperAdminLoginRequest $request)
    {

        $data = $request->validated();
        if (Auth::guard('superadmin')->attempt($data)) {
            $request->session()->regenerate();

            return redirect()->route('superadmin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login.view');
    }

    public function dashboard()
    {
        $totalAdmins = Admin::where('role', 'admin')->count();
        $activeAdmins = Admin::where('role', 'admin')->where('is_active', true)->count();
        $suspendedAdmins = Admin::where('role', 'admin')->where('is_active', false)->count();
        $totalUsers = User::count();
        $totalJobs = Job::count();

        return view('SuperAdmin.dashboard', compact(
            'totalAdmins',
            'activeAdmins',
            'suspendedAdmins',
            'totalUsers',
            'totalJobs'
        ));
    }

    public function adminList()
    {
        $admins = Admin::where('role', 'admin')->get();

        return view('SuperAdmin.admin_list', compact('admins'));
    }

    public function createForm()
    {
        if (auth('superadmin')->user()->role !== 'super_admin') {
            abort(403);
        }

        return view('SuperAdmin.create_admin');
    }

    public function createAdmin(CreateAdminRequest $request)
    {
        if (auth('superadmin')->user()->role !== 'super_admin') {
            abort(403);
        }

        $data = $request->validated();
        Admin::create([
            'company_name' => $data['company_name'],
            'contact_number' => $data['contact_number'],
            'location' => $data['location'],
            'description' => $data['description'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'New Admin Created Successfully!');

    }

    public function suspendAdmin($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);

        $admin->update(['is_active' => false]);

        return back()->with('success', " {$admin->company_name} suspended successfully.");
    }

    public function unsuspendAdmin($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);

        $admin->update(['is_active' => true]);

        return back()->with('success', " {$admin->company_name} reactivated successfully.");
    }

    public function deleteAdmin($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);
        $admin->delete();

        return back()->with('success', ' Admin deleted successfully.');
    }

    public function showAdmin($id)
    {
        $admin = Admin::where('role', 'admin')
            ->withCount(['jobs', 'jobs as active_jobs_count' => function ($q) {
                $q->where('last_date', '>=', now());
            }])
            ->findOrFail($id);

        return response()->json($admin);
    }
}
