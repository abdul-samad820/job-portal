<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;

class SuperAdminContactController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(15);
        $unreadCount = ContactMessage::unread()->count();

        return view('SuperAdmin.contact_messages', compact('messages', 'unreadCount'));
    }

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->markAsRead();

        return response()->json($message);
    }

    public function destroy($id)
    {
        ContactMessage::findOrFail($id)->delete();

        return back()->with('success', 'Message deleted.');
    }
}
