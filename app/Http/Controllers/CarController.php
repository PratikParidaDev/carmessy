<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Make;
use App\Models\CarModel;
use App\Models\City;
use App\Services\CarSearchService;


class CarController extends Controller
{
    public function __construct(
        private CarSearchService $searchService
    ) {}

    public function index(Request $request)
    {
        $query = $request->input('q');
        
        if ($query) {
            $cars = $this->searchService->search($request);
        } else {
            $cars = Car::approved()
                ->published()
                ->with(['make', 'model', 'city', 'dealer', 'media'])
                ->when($request->make_id, fn($q) => $q->where('make_id', $request->make_id))
                ->when($request->model_id, fn($q) => $q->where('model_id', $request->model_id))
                ->when($request->city_id, fn($q) => $q->where('city_id', $request->city_id))
                ->when($request->fuel_type, fn($q) => $q->where('fuel_type', $request->fuel_type))
                ->when($request->transmission, fn($q) => $q->where('transmission', $request->transmission))
                ->when($request->min_price, fn($q) => $q->where('price', '>=', $request->min_price))
                ->when($request->max_price, fn($q) => $q->where('price', '<=', $request->max_price))
                ->when($request->min_year, fn($q) => $q->where('year', '>=', $request->min_year))
                ->when($request->max_year, fn($q) => $q->where('year', '<=', $request->max_year))
                ->when($request->condition, fn($q) => $q->where('condition', $request->condition))
                ->when($request->sort === 'price_low', fn($q) => $q->orderBy('price', 'asc'))
                ->when($request->sort === 'price_high', fn($q) => $q->orderBy('price', 'desc'))
                ->when($request->sort === 'year_new', fn($q) => $q->orderBy('year', 'desc'))
                ->when($request->sort === 'mileage_low', fn($q) => $q->orderBy('mileage', 'asc'))
                ->latest('published_at')
                ->paginate(20);
        }

        $makes = Make::where('is_active', true)->orderBy('name')->get();
        $cities = City::where('is_active', true)->orderBy('name')->get();

        return view('cars.index', compact('cars', 'makes', 'cities'));
    }

    public function show(Car $car)
    {
        abort_if($car->status !== 'approved', 404);
        abort_if(!$car->published_at || $car->published_at->isFuture(), 404);

        $car->load(['make', 'model', 'city', 'dealer.city', 'media']);
        $car->incrementViews();

        $similarCars = Car::approved()
            ->published()
            ->where('id', '!=', $car->id)
            ->where(function ($query) use ($car) {
                $query->where('make_id', $car->make_id)
                      ->orWhere('model_id', $car->model_id)
                      ->orWhereBetween('price', [$car->price * 0.8, $car->price * 1.2]);
            })
            ->with(['make', 'model', 'city', 'media'])
            ->limit(6)
            ->get();

        return view('cars.show', compact('car', 'similarCars'));
    }

    public function compare(Request $request)
    {
        $carIds = $request->input('cars', []);
        
        if (count($carIds) < 2 || count($carIds) > 3) {
            return redirect()->route('cars.index')
                ->with('error', 'Please select 2-3 cars to compare');
        }

        $cars = Car::approved()
            ->published()
            ->whereIn('id', $carIds)
            ->with(['make', 'model', 'city', 'dealer', 'media'])
            ->get();

        if ($cars->count() < 2) {
            return redirect()->route('cars.index')
                ->with('error', 'Invalid cars selected');
        }

        return view('cars.compare', compact('cars'));
    }

    public function getModels(Request $request)
    {
        $models = CarModel::where('make_id', $request->make_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($models);
    }
 
}
