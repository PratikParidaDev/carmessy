<?php

namespace App\Http\Controllers\Dealer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'dealer']);
    }

    public function index()
    {
        $dealer = auth()->user()->dealer;
        
        $stats = [
            'total_cars' => $dealer->cars()->count(),
            'active_cars' => $dealer->cars()->where('status', 'approved')->count(),
            'pending_cars' => $dealer->cars()->where('status', 'pending')->count(),
            'total_views' => $dealer->cars()->sum('views'),
            'total_inquiries' => $dealer->cars()->sum('inquiries'),
        ];

        $recentCars = $dealer->cars()
            ->with(['make', 'model', 'city'])
            ->latest()
            ->take(5)
            ->get();

        $subscription = $dealer->activeSubscription;

        return view('dealer.dashboard', compact('dealer', 'stats', 'recentCars', 'subscription'));
    }

}
