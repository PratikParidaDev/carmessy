<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CarStatusController extends Controller
{
    /**
     * Get latest car status updates
     * This endpoint is polled by the frontend for real-time updates
     */
    public function getStatusUpdates(Request $request)
    {
        $lastCheck = $request->input('last_check', now()->subMinutes(5)->toIso8601String());
        
        // Get cars that were updated after the last check
        $updatedCars = Car::with(['make', 'model', 'city', 'dealer.user'])
            ->where('updated_at', '>', $lastCheck)
            ->get()
            ->map(function ($car) {
                return [
                    'id' => $car->id,
                    'status' => $car->status,
                    'title' => $car->title,
                    'make' => $car->make->name ?? null,
                    'model' => $car->model->name ?? null,
                    'price' => $car->price,
                    'published_at' => $car->published_at?->toIso8601String(),
                    'updated_at' => $car->updated_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'cars' => $updatedCars,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

