<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Inquiry;
use App\Notifications\NewInquiryNotification;

class InquiryController extends Controller
{
    public function store(Request $request, Car $car)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'nullable|string|max:1000',
        ]);

        $inquiry = $car->inquiries()->create(array_merge($validated, [
            'user_id' => auth()->id(),
        ]));

        $car->increment('inquiries');

        // Notify dealer
        $car->dealer->user->notify(new NewInquiryNotification($inquiry));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your inquiry has been sent successfully'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Your inquiry has been sent successfully');
    }

}
