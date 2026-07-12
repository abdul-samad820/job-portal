<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMail;
use App\Models\Admin;
use App\Models\ContactMessage;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function send(ContactRequest $request): RedirectResponse
    {
        // Rate limit: max 3 contact submissions per IP per hour
        $key = 'contact_form_'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withInput()
                ->with('contact_error', "Too many submissions. Please try again in {$seconds} seconds.");
        }

        RateLimiter::hit($key, 3600); // 1 hour decay

        $validated = $request->validated();

        // Always persist first — the message must never be lost even if
        // the mail server is down. SuperAdmin can review it in the inbox
        // regardless of email delivery status.
        $contactMessage = ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
        ]);

        // Alert every SuperAdmin in-app too — previously the only signal
        // was the email, which is easy to miss/spam-filter.
        Admin::where('role', 'super_admin')->get()->each(function ($superAdmin) use ($contactMessage) {
            $superAdmin->notify(new NewContactMessageNotification($contactMessage));
        });

        try {
            Mail::to(config('mail.contact_address', config('mail.from.address')))
                ->queue(new ContactMail(
                    $validated['name'],
                    $validated['email'],
                    $validated['subject'],
                    $validated['message']
                ));
        } catch (\Throwable $e) {
            // Don't fail the whole request just because email delivery
            // failed — the message is already safely stored above.
            Log::error('Contact form email failed to queue: '.$e->getMessage());
        }

        return back()->with('contact_success', 'Thank you! Your message has been sent. We will get back to you within 1–2 business days.');
    }
}
