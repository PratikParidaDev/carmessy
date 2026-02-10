<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Show the contact form
     */
    public function index()
    {
        return view('contact.index');
    }

    /**
     * Store the contact form submission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Store the contact submission
        $contact = Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        // Optionally send email notification to admin
        // You can uncomment and configure this if you want email notifications
        /*
        try {
            Mail::to(config('mail.admin_email', 'admin@example.com'))->send(new ContactFormMail($contact));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send contact form email: ' . $e->getMessage());
        }
        */

        return redirect()->route('contact.index')
            ->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
}
