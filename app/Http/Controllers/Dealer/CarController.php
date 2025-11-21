<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Make;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'dealer']);
    }

    public function index()
    {
        $cars = auth()->user()->dealer->cars()
            ->with(['make', 'model', 'city'])
            ->latest()
            ->paginate(20);

        return view('dealer.cars.index', compact('cars'));
    }

    public function create()
    {
        $makes = Make::where('is_active', true)->orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('dealer.cars.create', compact('makes', 'cities'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCar($request);
        
        DB::beginTransaction();
        try {
            $car = auth()->user()->dealer->cars()->create(array_merge($validated, [
                'status' => 'pending',
                'published_at' => null,
            ]));

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $car->addMedia($image)
                        ->toMediaCollection('images');
                }
            }

            DB::commit();

            return redirect()->route('dealer.cars.show', $car)
                ->with('success', 'Car listing created successfully and is pending approval');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create listing']);
        }
    }

    public function show(Car $car)
    {
        $this->authorize('view', $car);

        $car->load(['make', 'model', 'city', 'inquiries.user']);

        return view('dealer.cars.show', compact('car'));
    }

    public function edit(Car $car)
    {
        $this->authorize('update', $car);

        $makes = Make::where('is_active', true)->orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('dealer.cars.edit', compact('car', 'makes', 'cities'));
    }

    public function update(Request $request, Car $car)
    {
        $this->authorize('update', $car);

        $validated = $this->validateCar($request);
        
        DB::beginTransaction();
        try {
            $car->update($validated);

            // Handle new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $car->addMedia($image)
                        ->toMediaCollection('images');
                }
            }

            // Handle image deletions
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $mediaId) {
                    $car->media()->where('id', $mediaId)->delete();
                }
            }

            DB::commit();

            return redirect()->route('dealer.cars.show', $car)
                ->with('success', 'Car listing updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update listing']);
        }
    }

    public function destroy(Car $car)
    {
        $this->authorize('delete', $car);

        $car->delete();

        return redirect()->route('dealer.cars.index')
            ->with('success', 'Car listing deleted successfully');
    }

    private function validateCar(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'make_id' => 'required|exists:makes,id',
            'model_id' => 'required|exists:models,id',
            'city_id' => 'required|exists:cities,id',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:new,used,certified',
            'mileage' => 'nullable|integer|min:0',
            'vin' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'fuel_type' => 'required|in:petrol,diesel,electric,hybrid,cng,lpg',
            'transmission' => 'required|in:manual,automatic,semi-automatic',
            'engine_capacity' => 'nullable|string|max:255',
            'power' => 'nullable|integer|min:0',
            'torque' => 'nullable|integer|min:0',
            'mileage_kmpl' => 'nullable|numeric|min:0',
            'exterior_color' => 'required|string|max:255',
            'interior_color' => 'nullable|string|max:255',
            'seats' => 'required|integer|min:2|max:15',
            'doors' => 'required|integer|min:2|max:6',
            'features' => 'nullable|array',
            'safety_features' => 'nullable|array',
            'owners' => 'required|integer|min:0|max:10',
            'insurance_valid' => 'required|boolean',
            'insurance_expiry' => 'nullable|date|after:today',
            'under_warranty' => 'required|boolean',
            'service_history' => 'nullable|string',
            'description' => 'nullable|string|max:5000',
            'images.*' => 'nullable|image|max:5120', // 5MB max
        ]);
    }
}







?>