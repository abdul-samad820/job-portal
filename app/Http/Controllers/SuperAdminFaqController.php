<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

/**
 * Manages GLOBAL FAQs only (admin_id = null) — the ones shown on the
 * public homepage. Company-specific FAQs (admin_id set) remain owned
 * and managed by that Admin via FaqController and are not touched here.
 */
class SuperAdminFaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::whereNull('admin_id')->latest()->paginate(15);

        return view('SuperAdmin.faqs', compact('faqs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:5000',
        ]);

        Faq::create([
            'admin_id' => null, // global — this is what makes it public
            'question' => strip_tags($data['question']),
            'answer' => strip_tags($data['answer']),
            'status' => 1,
        ]);

        return back()->with('success', 'Global FAQ added successfully.');
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::whereNull('admin_id')->findOrFail($id);

        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:5000',
        ]);

        $faq->update([
            'question' => strip_tags($data['question']),
            'answer' => strip_tags($data['answer']),
        ]);

        return back()->with('success', 'FAQ updated successfully.');
    }

    public function toggle($id)
    {
        $faq = Faq::whereNull('admin_id')->findOrFail($id);
        $faq->update(['status' => ! $faq->status]);

        return back()->with('success', $faq->status ? 'FAQ shown on homepage.' : 'FAQ hidden from homepage.');
    }

    public function destroy($id)
    {
        $faq = Faq::whereNull('admin_id')->findOrFail($id);
        $faq->delete();

        return back()->with('success', 'FAQ deleted.');
    }
}
