<?php

namespace App\Http\Controllers;

use App\Models\JobRole;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class JobRoleController extends Controller
{
    use AuthorizesRequests;
    
    public function job_role(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
         $search = $request->search;
        $roles = JobRole::with('jobcategory')
            ->where('admin_id', $adminId)
               ->when($search, function($query) use ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }) 
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('Admin.job_role', compact('roles'));
    }

   public function job_role_add()
{
    $adminId = Auth::guard('admin')->id();
    $categories = JobCategory::where('admin_id', $adminId)->get();
    return view('Admin.job_role_add', compact('categories'));
}

public function job_role_create(Request $request)
{
    $adminId = Auth::guard('admin')->id();

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'category_id' => ['required','integer',
            Rule::exists('job_categories', 'id')->where(function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            }),
        ],
    ]);

    $data['admin_id'] = $adminId;
    JobRole::create($data);

    return redirect()->route('admin.job_role')->with('success', 'Role created successfully!');
}

public function job_role_edit($id)
{
    $role = JobRole::findOrFail($id);
    $this->authorize('update', $role);

    $adminId = Auth::guard('admin')->id();
    $categories = JobCategory::where('admin_id', $adminId)->get();

    return view('Admin.job_role_edit', compact('role', 'categories'));
}

    public function job_role_update(Request $request, $id)
{
    $role = JobRole::findOrFail($id);
    $this->authorize('update', $role);

    $adminId = Auth::guard('admin')->id();

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'category_id' => [
            'required', 
            'integer',
            Rule::exists('job_categories', 'id')
                ->where(function ($query) use ($adminId) {
                    $query->where('admin_id', $adminId);
                }),
        ],
    ]);

    $role->update($data);

    return redirect()
        ->route('admin.job_role')
        ->with('success', 'Role updated successfully!');
}

    public function job_role_delete($id)
    {
        $role = JobRole::findOrFail($id);
        $this->authorize('delete', $role);

        $role->delete();

        return redirect()->route('admin.job_role')->with('success', 'Role deleted successfully!');
    }
}
