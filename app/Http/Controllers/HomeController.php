<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Make;
use App\Models\City;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCars = Car::approved()
            ->published()
            ->featured()
            ->with(['make', 'model', 'city', 'dealer'])
            ->latest('published_at')
            ->take(8)
            ->get();

        $newCars = Car::approved()
            ->published()
            ->where('condition', 'new')
            ->with(['make', 'model', 'city'])
            ->latest('published_at')
            ->take(6)
            ->get();

        $usedCars = Car::approved()
            ->published()
            ->where('condition', 'used')
            ->with(['make', 'model', 'city'])
            ->latest('published_at')
            ->take(6)
            ->get();

        $popularMakes = Make::where('is_popular', true)
            ->where('is_active', true)
            ->orderBy('order')
            ->withCount('cars')
            ->get();

        $popularCities = City::where('is_popular', true)
            ->withCount('cars')
            ->having('cars_count', '>', 0)
            ->get();

        return view('home', compact(
            'featuredCars',
            'newCars',
            'usedCars',
            'popularMakes',
            'popularCities'
        ));
    }

}
