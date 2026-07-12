<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

/**
 * Platform-wide oversight of testimonials across ALL admins/companies.
 * Individual Admins can only moderate their own + global (admin_id null)
 * testimonials via TestimonialController — SuperAdmin can see and act on
 * every testimonial regardless of which company it's tied to, since a
 * bad/fake review reflects on the whole platform, not just one company.
 */
class SuperAdminTestimonialController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter');

        $testimonials = Testimonial::with('admin')
            ->when(in_array($filter, ['pending', 'approved', 'rejected'], true), function ($query) use ($filter) {
                $query->where('status', $filter);
            })
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pendingCount = Testimonial::pending()->count();

        return view('SuperAdmin.testimonials', compact('testimonials', 'filter', 'pendingCount'));
    }

    public function approve($id)
    {
        Testimonial::findOrFail($id)->update(['status' => Testimonial::STATUS_APPROVED]);

        return back()->with('success', 'Testimonial approved.');
    }

    public function reject($id)
    {
        Testimonial::findOrFail($id)->update(['status' => Testimonial::STATUS_REJECTED]);

        return back()->with('success', 'Testimonial rejected.');
    }

    public function destroy($id)
    {
        Testimonial::findOrFail($id)->delete();

        return back()->with('success', 'Testimonial permanently deleted.');
    }
}
