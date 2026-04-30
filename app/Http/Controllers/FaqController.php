<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function faqs()
    {
        $faqs = Faq::latest()->paginate(10);

        return view('admin.faqs', compact('faqs'));
    }

    public function faqs_create(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'answer' => 'required',

        ]);

        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => 1,
        ]);

        return back()->with('success', 'FAQ Added!');
    }

    public function faqs_edit($id)
    {
        $faq = Faq::findOrFail($id);

        return view('admin.faqs_edit', compact('faq'));
    }

    public function faqs_update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $request->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => $request->status ?? 1,
        ]);

        return redirect()->route('faq')->with('success', 'FAQ updated!');
    }

    public function faqs_delete($id)
    {
        $faqs_delete = Faq::findOrFail($id);
        $faqs_delete->delete();

        return redirect()->route('faq')->with('success', 'FAQ deleted successfully!');
    }
}
