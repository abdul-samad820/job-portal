<?php
namespace App\Http\Controllers;
use App\Models\User; 
use App\Models\Admin; 
use App\Models\JobRole; 
use App\Models\JobCategory; 
use App\Models\Job;
use App\Models\SavedJob;
use App\Models\JobApplication;
use App\Models\User_profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function userregister(Request $request)
{
    $credentials = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'phone' => 'required|digits:10',
        'address' => 'nullable|string|max:255',
    ]);

    // Hash password
    $credentials['password'] = bcrypt($credentials['password']);
    User::create($credentials);

    return redirect()->route('user.login')
        ->with('success', 'User Registration successful!');
}

   public function userlogin(Request $request)
{
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::guard('user')->attempt($data)) {

        $request->session()->regenerate();

        return redirect()
                ->route('user.dashboard')
                ->with('login_success', 'Welcome back, ' . auth('user')->user()->name . ' 👋');
    }

    return back()->withErrors([
        'email' => 'Invalid email or password.'
    ])->withInput();
}

  public function userlogout(Request $request){
     Auth::guard('user')->logout();  
     $request->session()->invalidate();  
    $request->session()->regenerateToken();  
    return redirect()->route('user.login');
  }

public function user_dashboard() 
{
    $userId = auth('user')->id();
    $user = Auth::guard('user')->user();

    $totalJobs = Job::count();

    $appliedJobsCount = JobApplication::where('user_id', $userId)->count();

    $newJobsCount = Job::where('created_at', '>=', now()->subDay())
        ->whereNotIn('id', JobApplication::where('user_id', $userId)->pluck('job_id'))
        ->count();

   $recentAppliedJobs = JobApplication::with(['job.admin'])->where('user_id', $userId)->latest()
   ->take(4)->get();

    $profile = User_profile::where('user_id', $userId)->first();

    $recommendedJobsCount = 0;

    if ($profile && !empty($profile->core_skills)) {

        $userSkills = array_map('trim', explode(',', $profile->core_skills));

        $recommendedJobsCount = Job::whereDate('last_date', '>=', now())
            ->where(function ($query) use ($userSkills) {
                foreach ($userSkills as $skill) {
                    $query->orWhere('required_skills', 'LIKE', '%' . $skill . '%');
                }
            })
            ->whereNotIn('id', JobApplication::where('user_id', $userId)->pluck('job_id'))
            ->count();
    }
// ==========================
    //  USER PROFILE COMPLETION
    // ==========================
$profileCompletion = 0;

$profile = User_profile::where('user_id', $userId)->first();

if ($profile) {

    // Profile Image
    if (!empty($profile->profile_image)) {
        $profileCompletion += 15;
    }

    // Professional Summary
    if (!empty($profile->professional_summary)) {
        $profileCompletion += 20;
    }

    // Core Skills
    if (!empty($profile->core_skills)) {
        $profileCompletion += 20;
    }

    // Education
    if (!empty($profile->education)) {
        $profileCompletion += 20;
    }

    // Experience
    if (!empty($profile->experience)) {
        $profileCompletion += 15;
    }
}

// Phone & Address from users table
if (!empty($user->phone) && !empty($user->address)) {
    $profileCompletion += 10;
}
$savedJobsCount = SavedJob::where('user_id', $userId)->count();
   $savedJobs = SavedJob::with('job')
        ->where('user_id', $userId)
        ->latest()
        ->take(3)
        ->get();

        $pendingCount = JobApplication::where('user_id', $userId)
    ->where('status', 'pending')
    ->count();

$shortlistedCount = JobApplication::where('user_id', $userId)
    ->where('status', 'shortlisted')
    ->count();

$hiredCount = JobApplication::where('user_id', $userId)
    ->where('status', 'hired')
    ->count();

$rejectedCount = JobApplication::where('user_id', $userId)
    ->where('status', 'rejected')
    ->count();

   return view('User.user_dashboard', compact(
    'totalJobs', 
    'appliedJobsCount', 
    'newJobsCount',
    'recentAppliedJobs',
    'recommendedJobsCount',
    'profileCompletion',
    'savedJobsCount',
    'savedJobs',
    'pendingCount',
    'shortlistedCount',
    'hiredCount',
    'rejectedCount'
));

}

public function save_job($jobId)
{
    $userId = Auth::guard('user')->id();

    SavedJob::firstOrCreate([
        'user_id' => $userId,
        'job_id' => $jobId
    ]);

    return back()->with('success', 'Job Saved Successfully!');
}

public function unsave_job($jobId)
{
    $userId = Auth::guard('user')->id();

    SavedJob::where('user_id', $userId)
        ->where('job_id', $jobId)
        ->delete();

    return back()->with('success', 'Job Removed from Saved!');
}

public function saved_jobs()
{
    $userId = Auth::guard('user')->id();

    $savedJobs = SavedJob::with('job')
        ->where('user_id', $userId)
        ->get();

    return view('User.user_saved_jobs', compact('savedJobs'));
}

public function user_jobs(Request $request)
{
    $userId = Auth::guard('user')->id();

    $query = Job::with(['category', 'role', 'admin']);

    // ✅ Saved + Applied IDs
    $savedJobIds = SavedJob::where('user_id', $userId)
                        ->pluck('job_id')
                        ->toArray();

    $appliedJobIds = JobApplication::where('user_id', $userId)
                        ->pluck('job_id')
                        ->toArray();

    //  Filters
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'LIKE', '%' . $request->search . '%')
              ->orWhere('description', 'LIKE', '%' . $request->search . '%');
        });
    }

    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    if ($request->filled('role')) {
        $query->where('role_id', $request->role);
    }

    if ($request->filled('location')) {
        $query->where('location', 'LIKE', '%' . $request->location . '%');
    }

    if ($request->filled('min_salary')) {
        $query->where('salary', '>=', $request->min_salary);
    }

    if ($request->filled('max_salary')) {
        $query->where('salary', '<=', $request->max_salary);
    }

    $jobs = $query->orderBy('id', 'desc')->paginate(5);

    $categories = JobCategory::all();
    $roles = JobRole::all();
    $totalUsers = User::count();

    return view('User.user_job_show', compact(
        'jobs',
        'categories',
        'roles',
        'totalUsers',
        'savedJobIds',   
        'appliedJobIds' 
    ));
}


 
public function user_job_single($id){
    $userId = Auth::guard('user')->id(); 
   $singlejob = Job::with(['category', 'role'])->findOrFail($id);

    $jobs = Job::where('category_id', $singlejob->category_id)
                ->where('id', '!=', $singlejob->id)
                ->take(6)
                ->get();

    return view('User.user_job_single', compact('singlejob', 'jobs'));
}
 

public function job_applied()
{
    $userId = Auth::guard('user')->id();

    $applications = JobApplication::with('job')
        ->where('user_id', $userId)
        ->latest()
        ->paginate(5);   
    return view('user.user_applied_jobs', compact('applications'));
}

public function User_profile()
{
    $user = Auth::guard('user')->user();
    $profile = User_profile::where('user_id', $user->id)->first();
    return view('User.profile', compact('user', 'profile'));
}
 

public function add_user_profile()
{
    $userId = Auth::guard('user')->id();
    $profile = User_profile::firstOrCreate(['user_id' => $userId]);
    $educationData = $profile->education ? json_decode($profile->education, true) : [];

    return view('User.profile_add', compact('profile', 'educationData'));
}

public function update_user_profile(Request $request)
{
    $userId = Auth::guard('user')->id();
    $profile = User_profile::firstOrCreate(['user_id' => $userId]);

    $data = $request->validate([
        'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'professional_summary' => 'nullable|string|max:2000',
        'core_skills' => 'nullable|string|max:500',
        'education' => 'nullable|array',
        'education.*.degree' => 'nullable|string|max:255',
        'education.*.institute' => 'nullable|string|max:255',
        'education.*.year' => 'nullable|string|max:20',
        'experience' => 'nullable|string|max:1255',
    ]);

    // ✅ Handle Profile Image (Storage System)
    if ($request->hasFile('profile_image')) {

        // Delete old image
        if ($profile->profile_image) {
            Storage::disk('public')
                ->delete('user_profile/' . $profile->profile_image);
        }

        // Store new image
        $path = $request->file('profile_image')
                        ->store('user_profile', 'public');

        $data['profile_image'] = basename($path);
    }

    // Education JSON
    if ($request->has('education') && is_array($request->education)) {
        $data['education'] = json_encode($request->education);
    }

    $profile->update($data);

    return redirect()->route('user.profile')
        ->with('success', 'Profile updated successfully!');
}

public function account_setting()
{
    $userId = Auth::guard('user')->id();
 $user_data = Auth::guard('user')->user();

    return view('User.user_account_setting', compact('user_data'));
}


public function account_setting_update(Request $request, $id)
{
    $user_data = User::findOrFail($id);

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'password' => 'nullable|min:6',
        'phone' => 'required|digits:10',
        'address' => 'nullable|string|max:255',
    ]);

    if (!empty($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    } else {
        unset($data['password']);
    }

    $user_data->update($data);
    return back()->with('success', 'Account details updated successfully!');
}
public function readNotifications()
{
    auth()->user()->unreadNotifications->markAsRead();
    return back();
}
}
