<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use App\Models\Job; 
use App\Models\JobApplication;
use Http\Middleware\Admin_mid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{

  public function login(Request $request){
 $data = $request->validate([
 'email'=>'required', 
 'password' => 'required', 
 ]);
if (Auth::guard('admin')->attempt($data)) {
   $request->session()->regenerate();
   return redirect()->route('admin.dashboard');
}else {
    return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
}
  }
  public function logout(Request $request){
      Auth::guard('admin')->logout();  
      $request->session()->invalidate();  
    $request->session()->regenerateToken(); 
    return redirect()->route('admin.login.view');
  }
  

  public function jobapplications(Request $request){
   $adminId = auth('admin')->id();
$jobIds = Job::where('admin_id', $adminId)->pluck('id');
$applications = JobApplication::with(['user','job'])
    ->whereIn('job_id', $jobIds)
    ->paginate(10);

     return view('Admin.job_application',compact('applications'));
  }

public function viewResume($id)
{
    $application = JobApplication::with('job')->findOrFail($id);

    
    if (auth('admin')->id() !== $application->job->admin_id) {
        abort(403, 'Unauthorized');
    }
    $storedPath = $application->resume;
    $relative = preg_replace('#^storage/#', '', $storedPath); 
    $disk = Storage::disk('public');

    if (! $disk->exists($relative)) {
        abort(404, 'Resume not found');
    }

    $fullPath = $disk->path($relative); 
    return response()->file($fullPath);

}

 public function updateApplicationStatus(Request $request, $id)
{
    $request->validate([
    'status' => 'required|in:pending,shortlisted,rejected,hired',
]);

    $application = JobApplication::findOrFail($id);
    $application->status = $request->status;
    $application->save();

    return back()->with('success', 'Application status updated successfully.');
}

public function dashboard()
{
    $adminId = Auth::guard('admin')->id();

    $jobIds = Job::where('admin_id', $adminId)->pluck('id');

    $totalJobs = Job::where('admin_id', $adminId)->count();

    $totalApplications = JobApplication::whereIn('job_id', $jobIds)->count();

    $totalSelected = JobApplication::whereIn('job_id', $jobIds)
        ->where('status', 'hired')
        ->count();

    $totalshortlisted = JobApplication::whereIn('job_id', $jobIds)
        ->where('status', 'shortlisted')
        ->count();

$dayLabels = [];
$dayValues = [];
for ($i = 6; $i >= 0; $i--) {
    $date = now()->subDays($i);
    $dayLabels[] = $date->format('D');
    $dayValues[] = JobApplication::whereIn('job_id', $jobIds)
        ->whereDate('created_at', $date)
        ->count();
}


$weekLabels = [];
$weekValues = [];
for ($i = 3; $i >= 0; $i--) {
    $start = now()->subWeeks($i)->startOfWeek();
    $end   = now()->subWeeks($i)->endOfWeek();
    $weekLabels[] = "Week " . $start->format('d');
    $weekValues[] = JobApplication::whereIn('job_id', $jobIds)
        ->whereBetween('created_at', [$start, $end])
        ->count();
}


$monthLabels = [];
$monthValues = [];
for ($i = 5; $i >= 0; $i--) {
    $month = now()->subMonths($i);
    $monthLabels[] = $month->format('M');
    $monthValues[] = JobApplication::whereIn('job_id', $jobIds)
        ->whereMonth('created_at', $month->month)
        ->count();
}

$chartLabels = [];
$appValues = [];
$hiredValues = [];

for ($i = 6; $i >= 0; $i--) {

    $date = now()->subDays($i)->format('Y-m-d');
    $chartLabels[] = now()->subDays($i)->format('D');  // Sun, Mon, Tue...

    $appValues[] = JobApplication::whereIn('job_id', $jobIds)
        ->whereDate('created_at', $date)
        ->count();

    $hiredValues[] = JobApplication::whereIn('job_id', $jobIds)
        ->where('status', 'hired')
        ->whereDate('created_at', $date)
        ->count();
}

    return view('Admin.admin_dashboard', compact(
        'totalJobs',
        'totalApplications',
        'totalSelected',
        'totalshortlisted',
        'chartLabels',
        'appValues',
        'hiredValues',
        'dayLabels','dayValues',
        'weekLabels','weekValues',
        'monthLabels','monthValues'
    ));
}

public function selectedList()
{
    $adminId = Auth::guard('admin')->id();

    $jobIds = Job::where('admin_id', $adminId)->pluck('id');

    $selectedApplicants = JobApplication::with('user', 'job')->whereIn('job_id', $jobIds)->whereIn('status', ['shortlisted', 'hired'])->get();

    return view('Admin.selectedList', compact('selectedApplicants'));
}

public function admin_profile(Request $request)
{
    $adminId = Auth::guard('admin')->id();
    $profile = Auth::guard('admin')->user();

     $totalJobs = Job::where('admin_id', $adminId)->count();
    $jobIds = Job::where('admin_id', $adminId)->pluck('id');
    $totalApplications = JobApplication::whereIn('job_id', $jobIds)->count();
    
    return view('Admin.profile', compact('profile', 'totalJobs', 'totalApplications'));
} 
public function update_profile(Request $request)
{
    $admin = Auth::guard('admin')->user();

    $data = $request->validate([
        'company_name' => 'required|string|max:255',
        'email' => 'required|email',
        'location' => 'nullable|string|max:255',
        'expertise' => 'nullable|string|max:1000',
        'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

   
    if ($request->hasFile('profile_image')) {

        if ($admin->profile_image && file_exists(public_path('uploads/admins/' . $admin->profile_image))) {
            unlink(public_path('uploads/admins/' . $admin->profile_image));
        }

        $file = $request->file('profile_image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/admins/'), $filename);

        $data['profile_image'] = $filename;
    }

    $admin->update($data);

    return back()->with('success', 'Profile updated successfully!');
}


}
