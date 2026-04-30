<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobRole;
use App\Models\SavedJob;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    use AuthorizesRequests;

    public function job(Request $request)
    {
        $adminId = auth('admin')->id();
        $search = trim($request->input('search'));
        $jobTypes = $request->input('job_type', []);
        $experiences = $request->input('experience', []);
        $category = $request->input('category');
        $role = $request->input('role');
        $minSalary = $request->input('min_salary');
        $maxSalary = $request->input('max_salary');
        $query = Job::with(['category', 'role'])->where('admin_id', $adminId);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if (! empty($jobTypes)) {
            $query->whereIn('type', $jobTypes);
        }
        if (! empty($experiences)) {
            $query->whereIn('experience', $experiences);
        }
        if (! empty($category)) {
            $query->where('category_id', $category);
        }
        if (! empty($role)) {
            $query->where('role_id', $role);
        }
        if (! empty($minSalary)) {
            $query->where('min_salary', '>=', $minSalary);
        }
        if (! empty($maxSalary)) {
            $query->where('max_salary', '<=', $maxSalary);
        }
        $jobs = $query->orderBy('id', 'desc')->paginate(5);
        $categories = JobCategory::where('admin_id', $adminId)->get();
        $roles = JobRole::where('admin_id', $adminId)->get();
        $experienceOptions = [
            'Fresher',
            '1 Year',
            '2 Years',
            '3 Years',
            '3+ Years',
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
        $userId = Auth::guard('user')->id();
        $search = trim($request->input('search'));
        $jobTypes = $request->input('job_type', []);
        $experiences = $request->input('experience', []);
        $category = $request->input('category');
        $role = $request->input('role');
        $sort = $request->input('sort');

        //  Use null check instead of empty() to handle 0 correctly
        $minSalary = $request->input('min_salary');
        $maxSalary = $request->input('max_salary');

        $query = Job::with(['category', 'role', 'admin']);

        if ($search) {
            $searchTerms = explode(' ', $search);
            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->where(function ($sub) use ($term) {
                        $sub->where('title', 'like', "%{$term}%")
                            ->orWhere('description', 'like', "%{$term}%")
                            ->orWhere('location', 'like', "%{$term}%")
                            ->orWhereHas('admin', function ($q2) use ($term) {
                                $q2->where('company_name', 'like', "%{$term}%");
                            });
                    });
                }
            });
        }

        if (! empty($jobTypes)) {
            //  Filter out the empty string the sort form sends
            $jobTypes = array_filter($jobTypes, fn ($v) => $v !== '');
            if (! empty($jobTypes)) {
                $query->whereIn('type', $jobTypes);
            }
        }

        if (! empty($experiences)) {
            //  Filter out empty strings
            $experiences = array_filter($experiences, fn ($v) => $v !== '');
            if (! empty($experiences)) {
                $query->whereIn('experience', $experiences);
            }
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        if ($role) {
            $query->where('role_id', $role);
        }

        //  Fix: Use is_numeric() so 0 is valid, and apply filter independently of sort
        if (is_numeric($minSalary) && is_numeric($maxSalary)) {
            $min = (int) $minSalary * 100000;
            $max = (int) $maxSalary * 100000;
            $query->where(function ($q) use ($min, $max) {
                $q->where('min_salary', '<=', $max)
                    ->where('max_salary', '>=', $min);
            });
        } elseif (is_numeric($minSalary)) {
            //  Fix: Handle only-min case
            $min = (int) $minSalary * 100000;
            $query->where('max_salary', '>=', $min);
        } elseif (is_numeric($maxSalary)) {
            // Fix: Handle only-max case
            $max = (int) $maxSalary * 100000;
            $query->where('min_salary', '<=', $max);
        }

        // Sorting —  No longer blocks salary filter
        switch ($sort) {
            case 'salary_low_high':
                $query->orderBy('min_salary', 'asc')->orderBy('id', 'desc');
                break;
            case 'salary_high_low':
                $query->orderBy('max_salary', 'desc')->orderBy('id', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $jobs = $query->paginate(5)->appends($request->query());

        $savedJobIds = SavedJob::where('user_id', $userId)->pluck('job_id')->toArray();
        $appliedJobIds = JobApplication::where('user_id', $userId)->pluck('job_id')->toArray();

        return view('User.user_job_show', [
            'jobs' => $jobs,
            'categories' => JobCategory::all(),
            'roles' => JobRole::all(),
            'savedJobIds' => $savedJobIds,
            'appliedJobIds' => $appliedJobIds,
        ]);
    }

    public function job_add()
    {
        $adminId = Auth::guard('admin')->id();
        $categories = JobCategory::where('admin_id', $adminId)->get();
        $roles = JobRole::where('admin_id', $adminId)->get();

        return view('Admin.job_add', compact('categories', 'roles'));
    }

    public function job_create(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'location' => 'required|string|max:255',
            'overview' => 'nullable|string|min:10|max:2000',
            'responsibilities' => 'nullable|string|min:10|max:3000',
            'required_skills' => 'nullable|string|min:5|max:2000',
            'experience' => 'nullable|in:Fresher,1 Year,2 Years,3 Years,3+ Years',
            'min_salary' => 'nullable|integer|min:0',
            'max_salary' => 'nullable|integer|min:0|gte:min_salary',
            'type' => 'required|string|in:Full-time,Part-time,Internship,Contract',
            'last_date' => 'nullable|date|after_or_equal:today',
            'category_id' => 'required|exists:job_categories,id',
            'role_id' => 'required|exists:job_roles,id',
            'job_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if (! is_null($data['min_salary'])) {
            $data['min_salary'] = $data['min_salary'] * 100000;
        }

        if (! is_null($data['max_salary'])) {
            $data['max_salary'] = $data['max_salary'] * 100000;
        }
        $data['admin_id'] = Auth::guard('admin')->id();
        if ($request->hasFile('job_image')) {
            $path = $request->file('job_image')->store('jobs', 'public');
            $data['job_image'] = $path;
        }
        Job::create($data);

        return redirect()->route('admin.job')->with('success', 'Job created successfully!');
    }

    public function job_edit($id)
    {
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'location' => 'required|string|max:255',
            'overview' => 'nullable|string|min:10|max:2000',
            'responsibilities' => 'nullable|string|min:10|max:3000',
            'required_skills' => 'nullable|string|min:5|max:2000',
            'experience' => 'nullable|in:Fresher,1 Year,2 Years,3 Years,3+ Years',
            'min_salary' => 'nullable|integer|min:0',
            'max_salary' => 'nullable|integer|min:0|gte:min_salary',
            'type' => 'required|string|in:Full-time,Part-time,Internship,Contract',
            'last_date' => 'nullable|date|after_or_equal:today',
            'category_id' => 'required|exists:job_categories,id',
            'role_id' => 'required|exists:job_roles,id',
            'job_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        if (! is_null($data['min_salary'])) {
            $data['min_salary'] = $data['min_salary'] * 100000;
        }

        if (! is_null($data['max_salary'])) {
            $data['max_salary'] = $data['max_salary'] * 100000;
        }
        if ($request->hasFile('job_image')) {
            if ($job->job_image) {
                Storage::disk('public')->delete($job->job_image);
            }
            $path = $request->file('job_image')->store('jobs', 'public');
            $data['job_image'] = $path;
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
