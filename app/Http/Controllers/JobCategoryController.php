<?php

namespace App\Http\Controllers;

use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class JobCategoryController extends Controller
{
    use AuthorizesRequests;

   public function job_category(Request $request)
{
    $search = $request->search;

    $categories = JobCategory::where('admin_id', Auth::guard('admin')->id())
        ->when($search, function($query) use ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        })
        ->orderBy('id', 'desc')
        ->paginate(10);

    return view('Admin.job_category', compact('categories'));
}

    public function job_category_create(Request $request)
{
    $data = $request->validate([
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'category_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $data['admin_id'] = Auth::guard('admin')->id();

    if ($request->hasFile('category_image')) {
        $file = $request->file('category_image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $request->file('category_image')
        ->store('categories', 'public');
$data['category_image'] = basename($path);
    }

    JobCategory::create($data);

    return redirect()->route('admin.job_category')
            ->with('success', 'Category created successfully!');
}

    public function job_category_edit($id)
    {
        $category = JobCategory::findOrFail($id);
        
        $this->authorize('update', $category);

        return view('Admin.job_category_edit', compact('category'));
    }
 
    public function job_category_update(Request $request, $id)
{
    $category = JobCategory::findOrFail($id);
    $this->authorize('update', $category);

    $data = $request->validate([
        'name'=> 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'category_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('category_image')) {

    // old image delete
    if ($category->category_image) {
        Storage::disk('public')
            ->delete('categories/' . $category->category_image);
    }

    $path = $request->file('category_image')
        ->store('categories', 'public');

    $data['category_image'] = basename($path);
}
    $category->update($data);

    return redirect()->route('admin.job_category')
        ->with('success', 'Category updated successfully!');
}

    public function job_category_delete($id)
    {
        $category = JobCategory::findOrFail($id);
        $this->authorize('delete', $category);

        $category->delete();

        return redirect()->route('admin.job_category')->with('success', 'Category deleted successfully!');
    }
}
