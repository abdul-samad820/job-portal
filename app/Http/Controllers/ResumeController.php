<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Traits\VerifiesUploadedFileMime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResumeController extends Controller
{
    use VerifiesUploadedFileMime;

    private const MAX_RESUMES_PER_USER = 5;

    /**
     * List the logged-in user's saved resumes.
     */
    public function index()
    {
        $userId = Auth::guard('user')->id();
        $resumes = Resume::where('user_id', $userId)->latest()->get();

        return view('User.resumes', compact('resumes'));
    }

    /**
     * Upload and save a new resume to the library.
     */
    public function store(Request $request)
    {
        $userId = Auth::guard('user')->id();

        $count = Resume::where('user_id', $userId)->count();
        if ($count >= self::MAX_RESUMES_PER_USER) {
            return back()->with('error',
                'You can only keep up to '.self::MAX_RESUMES_PER_USER.' resumes. Delete an old one first.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'resume' => 'required|file|mimes:pdf|max:2048',
        ]);

        $file = $request->file('resume');
        $path = $file->store('resumes', 'public');

        if (! $this->verifyStoredMime('public', $path, ['application/pdf'])) {
            return back()->with('error', 'Invalid file type. Please upload a valid PDF.');
        }

        $isFirst = $count === 0;

        $resume = Resume::create([
            'user_id' => $userId,
            'title' => $request->title,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            // The very first resume a user uploads becomes their default
            // automatically so applying stays a one-click action.
            'is_default' => $isFirst,
        ]);

        return back()->with('success', 'Resume "'.$resume->title.'" uploaded successfully.');
    }

    /**
     * Mark a resume as the default one used to pre-fill the apply form.
     */
    public function setDefault($id)
    {
        $userId = Auth::guard('user')->id();
        $resume = Resume::where('user_id', $userId)->where('id', $id)->firstOrFail();

        Resume::where('user_id', $userId)->update(['is_default' => false]);
        $resume->update(['is_default' => true]);

        return back()->with('success', '"'.$resume->title.'" is now your default resume.');
    }

    /**
     * Delete a resume from the library. Existing applications that used
     * it keep their own snapshot file, so nothing breaks for them.
     */
    public function destroy($id)
    {
        $userId = Auth::guard('user')->id();
        $resume = Resume::where('user_id', $userId)->where('id', $id)->firstOrFail();
        $wasDefault = $resume->is_default;

        $resume->delete();

        if ($wasDefault) {
            $next = Resume::where('user_id', $userId)->latest()->first();
            $next?->update(['is_default' => true]);
        }

        return back()->with('success', 'Resume deleted.');
    }

    /**
     * Download/open a resume file.
     */
    public function download($id)
    {
        $userId = Auth::guard('user')->id();
        $resume = Resume::where('user_id', $userId)->where('id', $id)->firstOrFail();

        if (! Storage::disk('public')->exists($resume->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->response(
            $resume->file_path,
            $resume->original_name ?? ($resume->title.'.pdf')
        );
    }
}
