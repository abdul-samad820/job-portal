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
class SuperAdminController extends Controller
{
 
    public function login(Request $request)
{
    $data = $request->validate([
        'email' => 'required',
        'password' => 'required',
    ]);

    if (Auth::guard('superadmin')->attempt($data)) {

        $request->session()->regenerate();
        return redirect()->route('superadmin.dashboard');
    }

    return back()->withErrors(['email' => 'Invalid credentials']);
}
  public function logout(Request $request){
    Auth::guard('superadmin')->logout(); 
      $request->session()->invalidate();  
    $request->session()->regenerateToken(); 
    return redirect()->route('superadmin.login.view');
  }
  
    public function dashboard()
    {
            $totalAdmins = Admin::where('role', 'admin')->count();
            $totalUsers = User::count();
            $totalJobs  = Job::count();
            return view('SuperAdmin.dashboard',compact('totalAdmins','totalUsers','totalJobs'));
        
    }

    public function adminList()
    {
        $admins = Admin::where('role', 'admin')->get();
        return view('SuperAdmin.admin_list', compact('admins'));
    }

   public function createForm(){
    if (auth('superadmin')->user()->role !== 'super_admin') {
        abort(403);
    }
    return view('SuperAdmin.create_admin');
}

public function createAdmin(Request $request)
{
   if (auth('superadmin')->user()->role !== 'super_admin') {
        abort(403);
    }

    $data = $request->validate([
    'company_name' => 'required|string|max:255',
    'contact_number' => 'nullable|digits_between:10,12',
    'location' => 'nullable|string|max:255',
    'description' => 'nullable|string|max:1080',
    'email' => 'required|email',
    'password' => 'required|confirmed|min:6',
    ]);

    Admin::create([
        'company_name' => $data['company_name'],
        'contact_number' => $data['contact_number'],
        'location' => $data['location'],
        'description' => $data['description'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']), 
        'role' => 'admin'
    ]);
 
   return redirect()
        ->route('superadmin.dashboard')
        ->with('success', 'New Admin Created Successfully!');
}
}
