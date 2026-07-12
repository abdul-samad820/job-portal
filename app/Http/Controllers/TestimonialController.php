<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\Testimonial;
use App\Traits\VerifiesUploadedFileMime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    use VerifiesUploadedFileMime;

    // ================================================================
    // ADMIN: moderation queue + CRUD
    // ================================================================

    public function testimonials(Request $request)
    {
        $adminId = Auth::guard('admin')->id();

        // Show this admin's own testimonials plus global ones (admin_id is null),
        // optionally filtered by moderation status via ?filter=pending|approved|rejected
        $filter = $request->query('filter');

        $testimonials = Testimonial::where(function ($query) use ($adminId) {
            $query->where('admin_id', $adminId)
                ->orWhereNull('admin_id');
        })
            ->when(in_array($filter, ['pending', 'approved', 'rejected'], true), function ($query) use ($filter) {
                $query->where('status', $filter);
            })
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END")
            ->orderBy('sort_order')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $pendingCount = Testimonial::pending()->count();

        return view('Admin.testimonials', compact('testimonials', 'filter', 'pendingCount'));
    }

    public function testimonials_create(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'review' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data['admin_id'] = Auth::guard('admin')->id();
        // Admin-authored testimonials are trusted content and go live
        // immediately — no moderation queue for the admin's own writing.
        $data['status'] = Testimonial::STATUS_APPROVED;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('testimonials', 'public');
            if (! $this->verifyStoredMime('public', $path, ['image/jpeg', 'image/png'])) {
                return back()->withErrors(['image' => 'Invalid file type.']);
            }
            $data['image'] = $path;
        }

        Testimonial::create($data);

        return back()->with('success', 'Testimonial added!');
    }

    public function testimonials_edit($id)
    {
        $testimonial = $this->moderatableTestimonialOrFail($id);

        return view('Admin.testimonials_edit', compact('testimonial'));
    }

    public function testimonials_update(Request $request, $id)
    {
        $testimonial = $this->moderatableTestimonialOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'review' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'status' => 'required|in:pending,approved,rejected',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }
            $path = $request->file('image')->store('testimonials', 'public');
            if (! $this->verifyStoredMime('public', $path, ['image/jpeg', 'image/png'])) {
                return back()->withErrors(['image' => 'Invalid file type.']);
            }
            $data['image'] = $path;
        }

        $testimonial->update($data);

        return redirect()->route('admin.testimonials')->with('success', 'Testimonial updated!');
    }

    public function testimonials_delete($id)
    {
        $testimonial = $this->moderatableTestimonialOrFail($id);

        if ($testimonial->image) {
            Storage::disk('public')->delete($testimonial->image);
        }
        $testimonial->delete();

        return redirect()->route('admin.testimonials')->with('success', 'Testimonial deleted successfully!');
    }

    /**
     * Approve a pending (usually user-submitted) testimonial so it
     * starts showing on the public homepage.
     */
    public function testimonials_approve($id)
    {
        $testimonial = $this->moderatableTestimonialOrFail($id);
        $testimonial->update(['status' => Testimonial::STATUS_APPROVED]);

        return back()->with('success', 'Testimonial approved and is now live on the homepage.');
    }

    /**
     * Reject a pending testimonial. It stays in the DB (so the same
     * applicant can't just resubmit endlessly) but never shows publicly.
     */
    public function testimonials_reject($id)
    {
        $testimonial = $this->moderatableTestimonialOrFail($id);
        $testimonial->update(['status' => Testimonial::STATUS_REJECTED]);

        return back()->with('success', 'Testimonial rejected.');
    }

    /**
     * Admin's own testimonials, or global ones (admin_id null) — covers
     * both admin-authored global content and user-submitted reviews,
     * which are always stored with admin_id = null so any admin can
     * moderate them.
     */
    private function moderatableTestimonialOrFail($id): Testimonial
    {
        $adminId = Auth::guard('admin')->id();

        return Testimonial::where('id', $id)
            ->where(function ($query) use ($adminId) {
                $query->where('admin_id', $adminId)
                    ->orWhereNull('admin_id');
            })
            ->firstOrFail();
    }

    // ================================================================
    // USER: submit a review after being hired
    // ================================================================

    /**
     * Show the "write a review" form for a hired application.
     */
    public function write_review($applicationId)
    {
        $application = $this->hiredApplicationOrFail($applicationId);

        if ($application->testimonial) {
            return redirect()->route('user.job_applied')
                ->with('info', 'You already submitted a review for this job.');
        }

        return view('User.write_review', compact('application'));
    }

    /**
     * Store a user-submitted review. Always goes in as PENDING —
     * an admin has to approve it before it appears on the homepage.
     */
    public function submit_review(Request $request, $applicationId)
    {
        $application = $this->hiredApplicationOrFail($applicationId);

        if ($application->testimonial) {
            return redirect()->route('user.job_applied')
                ->with('info', 'You already submitted a review for this job.');
        }

        $data = $request->validate([
            'designation' => 'nullable|string|max:255',
            'review' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::guard('user')->user();

        $payload = [
            'admin_id' => null, // user reviews are global platform content
            'user_id' => $user->id,
            'job_application_id' => $application->id,
            'name' => $user->name,
            'designation' => $data['designation'] ?? null,
            'company' => optional(optional($application->job)->admin)->company_name,
            'review' => $data['review'],
            'rating' => $data['rating'],
            'status' => Testimonial::STATUS_PENDING,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('testimonials', 'public');
            if (! $this->verifyStoredMime('public', $path, ['image/jpeg', 'image/png'])) {
                return back()->withErrors(['image' => 'Invalid file type.']);
            }
            $payload['image'] = $path;
        }

        Testimonial::create($payload);

        return redirect()->route('user.job_applied')
            ->with('success', 'Thanks for sharing your experience! Your review is pending admin approval.');
    }

    /**
     * Only the owning user can review their own application, and only
     * once it has actually been marked "hired" — prevents fake reviews
     * from applicants who were never selected.
     */
    private function hiredApplicationOrFail($applicationId): JobApplication
    {
        return JobApplication::where('id', $applicationId)
            ->where('user_id', Auth::guard('user')->id())
            ->where('status', 'hired')
            ->firstOrFail();
    }
}
