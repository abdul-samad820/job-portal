<?php

namespace App\Http\Controllers;

use App\Models\JobRole;
use App\Models\JobCategory;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class JobController extends Controller
{
    use AuthorizesRequests;   

public function job(Request $request)
{
    $adminId = auth('admin')->id();

    $search      = $request->input('search');
    $jobTypes    = $request->input('job_type', []);
    $experiences = $request->input('experience', []);
    $category    = $request->input('category');
    $role        = $request->input('role');
    $minSalary   = $request->input('min_salary');
    $maxSalary   = $request->input('max_salary');

    $query = Job::with(['category', 'role'])
                ->where('admin_id', $adminId);

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%")
              ->orWhere('salary', 'like', "%{$search}%");
        });
    }

    if (!empty($jobTypes)) {
        $query->whereIn('type', $jobTypes);
    }

    if (!empty($experiences)) {
        $query->whereIn('experience', $experiences);
    }

    if (!empty($category)) {
        $query->where('category_id', $category);
    }

    if (!empty($role)) {
        $query->where('role_id', $role);
    }

    if (!empty($minSalary)) {
        $query->whereRaw("REPLACE(salary, ' LPA', '') >= ?", [$minSalary]);
    }
    if (!empty($maxSalary)) {
        $query->whereRaw("REPLACE(salary, ' LPA', '') <= ?", [$maxSalary]);
    }

    $jobs = $query->orderBy('id', 'desc')->paginate(10);


    $categories = JobCategory::where('admin_id', $adminId)->get();
    $roles      = JobRole::where('admin_id', $adminId)->get();

    $experienceOptions = [
        'Fresher',
        '1 Year',
        '2 Years',
        '3 Years',
        '3+ Years'
    ]; 

    return view('Admin.job', [
        'jobs' => $jobs,
        'categories' => $categories,
        'roles' => $roles,
        'experienceOptions' => $experienceOptions,
    ]);
}

public function user_job_filter(Request $request)
{
    $search      = $request->input('search');
    $jobTypes    = $request->input('job_type', []);
    $experiences = $request->input('experience', []);
    $category    = $request->input('category');
    $role        = $request->input('role');
    $minSalary   = $request->input('min_salary'); 
    $maxSalary   = $request->input('max_salary');
    $sort        = $request->input('sort');

    $query = Job::with(['category', 'role']);

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%");
        });
    }

    if (!empty($jobTypes)) {
        $query->whereIn('type', $jobTypes);
    }

    if (!empty($experiences)) {
        $query->whereIn('experience', $experiences);
    }

    if ($category) $query->where('category_id', $category);
    if ($role)     $query->where('role_id', $role);

    $salaryExpr = "CAST(REGEXP_REPLACE(salary, '[^0-9.]','') AS UNSIGNED)"; // preferred
    $fallbackSalaryExpr = "CAST(REPLACE(salary, ' LPA', '') AS UNSIGNED)";

    if ($minSalary || $maxSalary) {
        try {
            if ($minSalary) {
                $query->whereRaw("{$salaryExpr} >= ?", [$minSalary]);
            }
            if ($maxSalary) {
                $query->whereRaw("{$salaryExpr} <= ?", [$maxSalary]);
            }
        } catch (\Exception $e) {
            if ($minSalary) {
                $query->whereRaw("{$fallbackSalaryExpr} >= ?", [$minSalary]);
            }
            if ($maxSalary) {
                $query->whereRaw("{$fallbackSalaryExpr} <= ?", [$maxSalary]);
            }
        }
    }

    $experienceOrder = "'Fresher','1 Year','2 Years','3 Years','3+ Years'";

    switch ($sort) {
        case 'latest':
            $query->orderBy('created_at', 'desc');
            break;

        case 'experience_asc':
            $query->orderByRaw("FIELD(experience, {$experienceOrder}) ASC");
            break;

        case 'experience_desc':
            $query->orderByRaw("FIELD(experience, {$experienceOrder}) DESC");
            break;

        case 'salary_low_high':
            try {
                $query->orderByRaw("{$salaryExpr} ASC");
            } catch (\Exception $e) {
                $query->orderByRaw("{$fallbackSalaryExpr} ASC");
            }
            break;

        case 'salary_high_low':
            try {
                $query->orderByRaw("{$salaryExpr} DESC");
            } catch (\Exception $e) {
                $query->orderByRaw("{$fallbackSalaryExpr} DESC");
            }
            break;

        default:
            $query->orderBy('created_at', 'desc');
            break;
    }

    $jobs = $query->paginate(10)->appends($request->query());

    return view('User.user_job_show', [
        'jobs' => $jobs,
        'categories' => JobCategory::all(),
        'roles' => JobRole::all(),
    ]);
}
   public function job_add(){
    $adminId = Auth::guard('admin')->id();
    $categories = JobCategory::where('admin_id', $adminId)->get();
    $roles = JobRole::where('admin_id', $adminId)->get();

    return view('Admin.job_add', compact('categories', 'roles'));
}

    public function job_create(Request $request){
     $data = $request->validate([
    'title'       => 'required|string|max:255',
    'description' => 'required|string|min:10',
    'location'    => 'required|string|max:255',
    'overview' => 'nullable|string|min:10|max:2000',
    'responsibilities' => 'nullable|string|min:10|max:3000',
    'required_skills' => 'nullable|string|min:5|max:2000',
    'experience' => 'nullable|in:Fresher,1 Year,2 Years,3 Years,3+ Years',
    'salary'      => 'nullable|regex:/^[₹\dA-Za-z\s.,+\-]*$/',
    'type'        => 'required|string|in:Full-time,Part-time,Internship,Contract',
    'last_date'   => 'nullable|date|after_or_equal:today',
    'category_id' => 'required|exists:job_categories,id',
    'role_id'     => 'required|exists:job_roles,id',
    'job_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
]);

 
        $data['admin_id'] = Auth::guard('admin')->id();

         if ($request->hasFile('job_image')) {
        $file = $request->file('job_image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/job/'), $filename);
        $data['job_image'] = $filename;
    }
        Job::create($data); 
        return redirect()->route('admin.job')->with('success', 'Job created successfully!');
    }

    public function job_edit($id){
    $job = Job::findOrFail($id);
    $this->authorize('update', $job);
    $adminId = Auth::guard('admin')->id();
    $categories = JobCategory::where('admin_id', $adminId)->get();
    $roles = JobRole::where('admin_id', $adminId)->get();
    return view('Admin.job_edit', compact('job', 'categories', 'roles'));
}

    public function job_update(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $this->authorize('update', $job);
       $data = $request->validate([
    'title'       => 'required|string|max:255',
    'description' => 'required|string|min:10',
    'location'    => 'required|string|max:255',
    'overview' => 'nullable|string|min:10|max:2000',
    'responsibilities' => 'nullable|string|min:10|max:3000',
    'required_skills' => 'nullable|string|min:5|max:2000',
    'experience' => 'nullable|in:Fresher,1 Year,2 Years,3 Years,3+ Years',
    'salary'      => 'nullable|regex:/^[₹\dA-Za-z\s.,+\-]*$/',
    'type'        => 'required|string|in:Full-time,Part-time,Internship,Contract',
    'last_date'   => 'nullable|date|after_or_equal:today',
    'category_id' => 'required|exists:job_categories,id',
    'role_id'     => 'required|exists:job_roles,id',
    'job_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
]);

    if ($request->hasFile('job_image')) {

        if ($job->job_image && file_exists(public_path('uploads/job/' . $job->job_image))) {
            unlink(public_path('uploads/job/' . $job->job_image));
        }

        $file = $request->file('job_image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/job/'), $filename);

        $data['job_image'] = $filename;
    }

        $job->update($data);
 
        return redirect()->route('admin.job')->with('success', 'Job updated successfully!');
    }

    public function job_delete($id)
    {
        $job = Job::findOrFail($id);
        $this->authorize('delete', $job);  

        $job->delete();

        return redirect()->route('admin.job')->with('success', 'Job deleted successfully!');
    }
}
 