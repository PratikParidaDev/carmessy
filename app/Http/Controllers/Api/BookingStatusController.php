<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookingStatusController extends Controller
{
    /**
     * Get latest booking status updates
     * This endpoint is polled by the frontend for real-time updates
     */
    public function getStatusUpdates(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        try {
            $lastCheck = $request->input('last_check', now()->subMinutes(5)->toIso8601String());
            
            // Parse the last check time
            $lastCheckTime = \Carbon\Carbon::parse($lastCheck);
            
            // Get bookings for this user that were updated after the last check
            $updatedBookings = Booking::where('user_id', $user->id)
                ->with(['vehicle.make', 'vehicle.model'])
                ->where('updated_at', '>', $lastCheckTime)
                ->get()
                ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'vehicle_name' => $booking->vehicle_type === 'car' && $booking->vehicle 
                        ? ($booking->vehicle->make->name ?? '') . ' ' . ($booking->vehicle->model->name ?? '')
                        : 'Vehicle #' . $booking->vehicle_id,
                    'preferred_booking_date' => $booking->preferred_booking_date->format('Y-m-d'),
                    'preferred_time_slot' => $booking->preferred_time_slot,
                    'updated_at' => $booking->updated_at->toIso8601String(),
                ];
            });

            return response()->json([
                'success' => true,
                'bookings' => $updatedBookings,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Booking status update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching updates',
                'bookings' => [],
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }
}
