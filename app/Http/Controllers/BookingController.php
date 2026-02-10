<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use App\Models\TimeSlot;
use App\Models\PickupType;
use App\Models\PaymentMode;
use App\Models\IdProofType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    /**
     * Show the booking form for a specific vehicle
     */
    public function create(Request $request, $vehicleType, $id)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login or register to book this vehicle.');
        }

        // Validate vehicle type
        if (!in_array($vehicleType, ['car', 'bike'])) {
            abort(404);
        }

        // Get the vehicle
        if ($vehicleType === 'car') {
            $vehicle = Car::where('id', $id)
                ->where('status', 'approved')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->firstOrFail();
        } else {
            // For future bike implementation
            abort(404, 'Bike booking not yet implemented');
        }

        $user = Auth::user();
        
        // Get active time slots
        $timeSlots = TimeSlot::active()->ordered()->get();
        
        // Get active pickup types, payment modes, and ID proof types
        $pickupTypes = PickupType::active()->ordered()->get();
        $paymentModes = PaymentMode::active()->ordered()->get();
        $idProofTypes = IdProofType::active()->ordered()->get();

        return view('bookings.create', compact('vehicle', 'vehicleType', 'user', 'timeSlots', 'pickupTypes', 'paymentModes', 'idProofTypes'));
    }

    /**
     * Store a new booking
     */
    public function store(Request $request)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login or register to book this vehicle.');
        }

        // Get valid values from database
        $validPickupTypeSlugs = PickupType::active()->pluck('slug')->toArray();
        $validPaymentModeSlugs = PaymentMode::active()->pluck('slug')->toArray();
        $validIdProofTypeSlugs = IdProofType::active()->pluck('slug')->toArray();
        $validTimeSlots = TimeSlot::active()->pluck('name')->toArray();

        // Validate the request
        $validated = $request->validate([
            'vehicle_id' => ['required', 'integer'],
            'vehicle_type' => ['required', 'string', Rule::in(['car', 'bike'])],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'city' => ['required', 'string', 'max:255'],
            'preferred_booking_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time_slot' => ['required', 'string', Rule::in($validTimeSlots)],
            'pickup_type' => ['required', 'string', Rule::in($validPickupTypeSlugs)],
            'payment_mode' => ['required', 'string', Rule::in($validPaymentModeSlugs)],
            'id_proof_type' => ['required', 'string', Rule::in($validIdProofTypeSlugs)],
            'id_proof_number' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        // Verify vehicle exists and is available
        if ($validated['vehicle_type'] === 'car') {
            $vehicle = Car::where('id', $validated['vehicle_id'])
                ->where('status', 'approved')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->first();

            if (!$vehicle) {
                return back()->withErrors(['vehicle_id' => 'The selected vehicle is not available for booking.'])->withInput();
            }
        } else {
            return back()->withErrors(['vehicle_type' => 'Bike booking is not yet available.'])->withInput();
        }

        // Create the booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $validated['vehicle_id'],
            'vehicle_type' => $validated['vehicle_type'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'city' => $validated['city'],
            'preferred_booking_date' => $validated['preferred_booking_date'],
            'preferred_time_slot' => $validated['preferred_time_slot'],
            'pickup_type' => $validated['pickup_type'],
            'payment_mode' => $validated['payment_mode'],
            'id_proof_type' => $validated['id_proof_type'],
            'id_proof_number' => $validated['id_proof_number'],
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('bookings.my-bookings')
            ->with('success', 'Your booking has been submitted successfully! We will contact you soon.');
    }

    /**
     * Show user's bookings
     */
    public function myBookings()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $bookings = Booking::where('user_id', Auth::id())
            ->with(['vehicle.make', 'vehicle.model', 'vehicle.city'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bookings.my-bookings', compact('bookings'));
    }
}
