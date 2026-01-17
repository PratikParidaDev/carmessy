<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Make;
use App\Models\City;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache homepage data for 15 minutes
        $cacheKey = 'homepage:data';
        
        $data = Cache::remember($cacheKey, 900, function () {
            $featuredCars = Car::approved()
                ->published()
                ->featured()
                ->with(['make', 'model', 'city', 'dealer', 'media'])
                ->latest('published_at')
                ->take(8)
                ->get();

            $newCars = Car::approved()
                ->published()
                ->where('condition', 'new')
                ->with(['make', 'model', 'city', 'media'])
                ->latest('published_at')
                ->take(6)
                ->get();

            $usedCars = Car::approved()
                ->published()
                ->where('condition', 'used')
                ->with(['make', 'model', 'city', 'dealer', 'media'])
                ->latest('published_at')
                ->take(6)
                ->get();

            $popularMakes = Make::where('is_popular', true)
                ->where('is_active', true)
                ->orderBy('order')
                ->withCount('cars')
                ->get();

            $popularCities = City::where('is_popular', true)->where('is_active', true)
                ->withCount('cars')
                ->having('cars_count', '>', 0)
                ->get();

            return compact(
                'featuredCars',
                'newCars',
                'usedCars',
                'popularMakes',
                'popularCities'
            );
        });

        return view('home', $data);
    }

}
