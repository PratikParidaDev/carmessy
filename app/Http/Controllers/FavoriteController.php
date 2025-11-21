<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $favorites = auth()->user()
            ->favorites()
            ->with(['car.make', 'car.model', 'car.city'])
            ->latest()
            ->paginate(20);

        return view('favorites.index', compact('favorites'));
    }

    public function store(Car $car)
    {
        $exists = Favorite::where('user_id', auth()->id())
            ->where('car_id', $car->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Already in favorites'
            ], 400);
        }

        Favorite::create([
            'user_id' => auth()->id(),
            'car_id' => $car->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to favorites'
        ]);
    }

    public function destroy(Car $car)
    {
        Favorite::where('user_id', auth()->id())
            ->where('car_id', $car->id)
            ->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Removed from favorites'
            ]);
        }

        return redirect()->back()->with('success', 'Removed from favorites');
    }

}
