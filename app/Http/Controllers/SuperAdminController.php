<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use App\Models\User;
use App\Models\Job;
use App\Models\JobApplication; 
use Http\Middleware\SuperAdminOnly;
use Http\Middleware\Admin_mid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SuperAdminLoginRequest;
use App\Http\Requests\CreateAdminRequest;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller {
 
    public function login(SuperAdminLoginRequest $request) {

    $data = $request->validated();
    if (Auth::guard('superadmin')->attempt($data)) {
        $request->session()->regenerate();
        return redirect()->route('superadmin.dashboard');
}  
       return back()->withErrors([
       'email' => 'Invalid email or password.'])->withInput();
}

public function logout(Request $request) {
    Auth::guard('superadmin')->logout(); 
    $request->session()->invalidate();  
    $request->session()->regenerateToken(); 
    return redirect()->route('superadmin.login.view');
}
  
public function dashboard() {
            $totalAdmins = Admin::where('role', 'admin')->count();
            $totalUsers = User::count();
            $totalJobs  = Job::count();
            return view('SuperAdmin.dashboard',compact('totalAdmins','totalUsers','totalJobs'));  
}

public function adminList() {
        $admins = Admin::where('role', 'admin')->get();
        return view('SuperAdmin.admin_list', compact('admins'));
        }

public function createForm() {
    if (auth('superadmin')->user()->role !== 'super_admin') {
        abort(403);
}
       return view('SuperAdmin.create_admin');
}

public function createAdmin(CreateAdminRequest $request) {
   if (auth('superadmin')->user()->role !== 'super_admin') {
        abort(403);
}

       $data = $request->validated();
       Admin::create([
        'company_name'   => $data['company_name'],
        'contact_number' => $data['contact_number'],
        'location'       => $data['location'],
        'description'    => $data['description'],
        'email'          => $data['email'],
        'password'       => Hash::make($data['password']),
        'role'           => 'admin'
]);

    return redirect()->route('superadmin.dashboard')
    ->with('success', 'New Admin Created Successfully!');

}
}
