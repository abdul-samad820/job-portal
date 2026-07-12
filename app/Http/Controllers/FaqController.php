<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    public function faqs()
    {
        $adminId = Auth::guard('admin')->id();

        // Show this admin's own FAQs plus global ones (admin_id is null)
        $faqs = Faq::where(function ($query) use ($adminId) {
            $query->where('admin_id', $adminId)
                ->orWhereNull('admin_id');
        })
            ->latest()
            ->paginate(10);

        return view('Admin.faqs', compact('faqs'));
    }

    public function faqs_create(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:5000',
        ]);

        Faq::create([
            'admin_id' => Auth::guard('admin')->id(),
            'question' => strip_tags($request->question),
            'answer' => strip_tags($request->answer),
            'status' => 1,
        ]);

        return back()->with('success', 'FAQ Added!');
    }

    public function faqs_edit($id)
    {
        $faq = $this->ownedFaqOrFail($id);

        return view('Admin.faqs_edit', compact('faq'));
    }

    public function faqs_update(Request $request, $id)
    {
        $faq = $this->ownedFaqOrFail($id);

        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:5000',
        ]);

        $faq->update([
            'question' => strip_tags($request->question),
            'answer' => strip_tags($request->answer),
            'status' => $request->status ?? 1,
        ]);

        return redirect()->route('faq')->with('success', 'FAQ updated!');
    }

    public function faqs_delete($id)
    {
        $faqs_delete = $this->ownedFaqOrFail($id);
        $faqs_delete->delete();

        return redirect()->route('faq')->with('success', 'FAQ deleted successfully!');
    }

    /**
     * Only this admin's own FAQs (or global ones) can be edited/deleted —
     * prevents one admin from tampering with another company's FAQs.
     */
    private function ownedFaqOrFail($id): Faq
    {
        $adminId = Auth::guard('admin')->id();

        return Faq::where('id', $id)
            ->where(function ($query) use ($adminId) {
                $query->where('admin_id', $adminId)
                    ->orWhereNull('admin_id');
            })
            ->firstOrFail();
    }
}
