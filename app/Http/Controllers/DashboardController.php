<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Make;
use App\Models\City;
use App\Services\CarSchemaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $schemaService;

    public function __construct(CarSchemaService $schemaService)
    {
        $this->schemaService = $schemaService;
    }

    public function index()
    {
        $user = auth()->user();
        
        // Get user's cars if they have dealer role or dealer profile
        $cars = collect();
        $stats = [
            'total_cars' => 0,
            'pending_cars' => 0,
            'approved_cars' => 0,
            'rejected_cars' => 0,
            'sold_cars' => 0,
        ];

        $listingFields = null;

        // Check if user is a dealer, admin, or has dealer profile (buyers who posted cars)
        if ($user->isDealer() || $user->dealer || $user->isAdmin()) {
            // Admin can see all cars, dealers see only their own
            if ($user->isAdmin()) {
                // Admin: Get all cars
                $cars = Car::with(['make', 'model', 'city', 'dealer', 'media'])
                    ->latest()
                    ->paginate(20);

                // Admin stats (all cars)
                $stats = [
                    'total_cars' => Car::count(),
                    'pending_cars' => Car::where('status', 'pending')->count(),
                    'approved_cars' => Car::where('status', 'approved')->count(),
                    'rejected_cars' => Car::where('status', 'rejected')->count(),
                    'sold_cars' => Car::where('status', 'sold')->count(),
                ];
            } elseif ($user->dealer) {
                // Dealer or buyer with dealer profile: Get only their own cars
                $cars = $user->dealer->cars()
                    ->with(['make', 'model', 'city', 'media'])
                    ->latest()
                    ->paginate(20);

                // Stats (own cars only)
                $stats = [
                    'total_cars' => $user->dealer->cars()->count(),
                    'pending_cars' => $user->dealer->cars()->where('status', 'pending')->count(),
                    'approved_cars' => $user->dealer->cars()->where('status', 'approved')->count(),
                    'rejected_cars' => $user->dealer->cars()->where('status', 'rejected')->count(),
                    'sold_cars' => $user->dealer->cars()->where('status', 'sold')->count(),
                ];
            } else {
                // No dealer profile yet, but user has dealer role - show empty state
                $cars = new \Illuminate\Pagination\LengthAwarePaginator(
                    collect([]),
                    0,
                    20,
                    1
                );
            }

            // Get listing fields for dynamic table
            $listingFields = $this->schemaService->getListingFields();
        }

        return view('dashboard', [
            'section' => 'overview',
            'cars' => $cars,
            'stats' => $stats,
            'listingFields' => $listingFields,
        ]);
    }

    public function myCars()
    {
        $user = auth()->user();

        // Admin can see all cars
        if ($user->isAdmin()) {
            $cars = Car::with(['make', 'model', 'city', 'dealer', 'media'])
                ->latest()
                ->paginate(20);
        } elseif ($user->dealer) {
            // Dealers or buyers with dealer profile see only their own cars
            $cars = $user->dealer->cars()
                ->with(['make', 'model', 'city', 'media'])
                ->latest()
                ->paginate(20);
        } else {
            // No dealer profile - redirect to create car page
            return redirect()->route('dashboard.cars.create')
                ->with('info', 'Post your first car to get started!');
        }

        $listingFields = $this->schemaService->getListingFields();

        return view('dashboard', [
            'section' => 'my-cars',
            'cars' => $cars,
            'listingFields' => $listingFields,
        ]);
    }

    public function createCar()
    {
        $user = auth()->user();

        // All authenticated users can create cars (buyers/sellers will auto-get dealer profile)
        // If user doesn't have dealer profile, create one automatically
        if (!$user->dealer) {
            // Get first city or default to 1
            $cityId = \App\Models\City::first()->id ?? 1;
            
            $dealer = \App\Models\Dealer::create([
                'user_id' => $user->id,
                'business_name' => $user->name . ' Auto Dealer',
                'phone' => '',
                'address' => '',
                'city_id' => $cityId,
                'pincode' => '',
            ]);
            $user->refresh();
        }

        $makes = Make::where('is_active', true)->orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $fields = $this->schemaService->getEditableFields(true); // true = for create form
        
        // Add images field manually (not in database schema, handled by media library)
        $fields['images'] = [
            'name' => 'images',
            'label' => 'Car Images',
            'type' => 'file',
            'required' => true,
            'nullable' => false,
            'multiple' => true,
            'accept' => 'image/*',
        ];

        return view('dashboard', [
            'section' => 'create-car',
            'makes' => $makes,
            'cities' => $cities,
            'fields' => $fields,
        ]);
    }

    public function storeCar(Request $request)
    {
        $user = auth()->user();

        // All authenticated users can create cars (buyers/sellers will auto-get dealer profile)
        // If user doesn't have dealer profile, create one automatically
        $roleChanged = false;
        if (!$user->dealer) {
            // Get first city or default to 1
            $cityId = \App\Models\City::first()->id ?? 1;
            
            $dealer = \App\Models\Dealer::create([
                'user_id' => $user->id,
                'business_name' => $user->name . ' Auto Dealer',
                'phone' => '',
                'address' => '',
                'city_id' => $cityId,
                'pincode' => '',
            ]);
            $user->refresh();
            
            // Auto-upgrade buyer to dealer when posting first car
            if ($user->isBuyer()) {
                $user->role = 'dealer';
                $user->save();
                $user->refresh();
                $roleChanged = true;
            }
        }

        // Set condition to 'used' for non-admin users before validation
        // This ensures validation passes since condition field is hidden in create form
        if (!$user->isAdmin() && !$request->has('condition')) {
            $request->merge(['condition' => 'used']);
        }

        $validated = $this->validateCar($request);
        
        DB::beginTransaction();
        try {
            
            // Admin can assign any dealer, dealers can only create for themselves
            if ($user->isAdmin() && $request->has('dealer_id')) {
                $dealerId = $request->dealer_id;
            } else {
                $dealerId = $user->dealer->id;
            }

            // Set business rules: condition = 'used', status = 'pending' (unless admin overrides)
            $car = Car::create(array_merge($validated, [
                'dealer_id' => $dealerId,
                'condition' => $user->isAdmin() && $request->has('condition') ? $request->condition : 'used',
                'status' => $user->isAdmin() && $request->has('status') ? $request->status : 'pending',
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

            $successMessage = 'Car listing created successfully and is pending approval';
            if ($roleChanged) {
                $successMessage .= '. Your account has been automatically upgraded from Buyer to Dealer!';
            }

            return redirect()->route('dashboard.my-cars')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create listing: ' . $e->getMessage()]);
        }
    }

    public function editCar(Car $car)
    {
        $user = auth()->user();

        // Admin can edit any car, dealers can only edit their own
        if (!$user->isAdmin()) {
            if (!$user->dealer || $car->dealer_id !== $user->dealer->id) {
                abort(403, 'Unauthorized access');
            }
        }

        $makes = Make::where('is_active', true)->orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $fields = $this->schemaService->getEditableFields();
        
        // Add images field manually (not in database schema, handled by media library)
        $fields['images'] = [
            'name' => 'images',
            'label' => 'Car Images',
            'type' => 'file',
            'required' => false, // Not required for edit (can keep existing images)
            'nullable' => true,
            'multiple' => true,
            'accept' => 'image/*',
        ];

        return view('dashboard', [
            'section' => 'edit-car',
            'car' => $car->load(['make', 'model', 'city', 'media']),
            'makes' => $makes,
            'cities' => $cities,
            'fields' => $fields,
        ]);
    }

    public function updateCar(Request $request, Car $car)
    {
        $user = auth()->user();

        // Admin can update any car, dealers can only update their own
        if (!$user->isAdmin()) {
            if (!$user->dealer || $car->dealer_id !== $user->dealer->id) {
                abort(403, 'Unauthorized access');
            }
        }

        $validated = $this->validateCar($request);
        
        // Additional validation: At least one image required (new or existing)
        $hasNewImages = $request->hasFile('images') && count($request->file('images')) > 0;
        $hasExistingImages = $request->has('existing_images') && count($request->existing_images) > 0;
        $willHaveImagesAfterDelete = false;
        
        // Check if images will exist after deletion
        if ($request->has('delete_images') && !empty($request->delete_images)) {
            $deleteIds = is_array($request->delete_images) 
                ? $request->delete_images 
                : explode(',', $request->delete_images);
            $currentImageCount = $car->getMedia('images')->count();
            $willHaveImagesAfterDelete = ($currentImageCount - count(array_filter($deleteIds)) + ($hasNewImages ? count($request->file('images')) : 0)) > 0;
        } else {
            $willHaveImagesAfterDelete = $hasNewImages || $hasExistingImages || $car->getMedia('images')->count() > 0;
        }
        
        if (!$willHaveImagesAfterDelete) {
            return back()->withInput()->withErrors(['images' => 'At least one image is required. You cannot delete all images.']);
        }
        
        DB::beginTransaction();
        try {
            // Business rule: If dealer edits an approved car, reset status to 'pending'
            // Admin can edit without resetting status
            if (!$user->isAdmin() && $car->status === 'approved') {
                $validated['status'] = 'pending';
                $validated['published_at'] = null;
            }

            $car->update($validated);

            // Handle new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $car->addMedia($image)
                        ->toMediaCollection('images');
                }
            }

            // Handle image deletions (WordPress-style sends comma-separated string)
            if ($request->has('delete_images') && !empty($request->delete_images)) {
                $deleteIds = is_array($request->delete_images) 
                    ? $request->delete_images 
                    : explode(',', $request->delete_images);
                
                foreach ($deleteIds as $mediaId) {
                    if (!empty($mediaId)) {
                        $car->media()->where('id', $mediaId)->delete();
                    }
                }
            }

            DB::commit();

            $message = $car->status === 'pending' 
                ? 'Car listing updated successfully. Status reset to pending for admin review.'
                : 'Car listing updated successfully';

            return redirect()->route('dashboard.my-cars')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update listing: ' . $e->getMessage()]);
        }
    }

    public function deleteCar(Car $car)
    {
        $user = auth()->user();

        // Admin can delete any car, dealers can only delete their own
        if (!$user->isAdmin()) {
            if (!$user->dealer || $car->dealer_id !== $user->dealer->id) {
                abort(403, 'Unauthorized access');
            }
        }

        $car->delete();

        return redirect()->route('dashboard.my-cars')
            ->with('success', 'Car listing deleted successfully');
    }

    public function profile()
    {
        $user = auth()->user();
        $user->load(['dealer.city']);
        
        // Get user statistics
        $stats = [
            'total_cars' => $user->dealer ? $user->dealer->cars()->count() : 0,
            'favorites_count' => $user->favorites()->count(),
            'reviews_count' => $user->reviews()->count(),
            'inquiries_count' => $user->dealer ? $user->dealer->cars()->withCount('inquiries')->get()->sum('inquiries_count') : 0,
        ];

        return view('dashboard', [
            'section' => 'profile',
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    private function validateCar(Request $request): array
    {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        
        return $request->validate([
            'title' => 'required|string|max:255',
            'make_id' => 'required|exists:makes,id',
            'model_id' => 'required|exists:models,id',
            'city_id' => 'required|exists:cities,id',
            'year' => 'required|integer|min:1900|max:' . $nextYear,
            'price' => 'required|numeric|min:1000',
            'condition' => 'required|in:new,used,certified',
            'mileage' => 'nullable|integer|min:0',
            'vin' => 'nullable|string|max:255|regex:/^[A-HJ-NPR-Z0-9]{17}$/i',
            'registration_number' => 'nullable|string|max:255',
            'fuel_type' => 'required|in:petrol,diesel,electric,hybrid,cng,lpg',
            'transmission' => 'required|in:manual,automatic,semi-automatic',
            'engine_capacity' => 'nullable|string|max:255',
            'power' => 'nullable|integer|min:0|max:2000',
            'torque' => 'nullable|integer|min:0|max:5000',
            'mileage_kmpl' => 'nullable|numeric|min:0|max:100',
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
            'service_history' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:5000',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB max
            'existing_images' => 'nullable|array',
        ], [
            // Custom error messages
            'title.required' => 'Car title is required.',
            'title.max' => 'Car title cannot exceed 255 characters.',
            'make_id.required' => 'Please select a car make.',
            'make_id.exists' => 'Selected make is invalid.',
            'model_id.required' => 'Please select a car model.',
            'model_id.exists' => 'Selected model is invalid.',
            'city_id.required' => 'Please select a city.',
            'city_id.exists' => 'Selected city is invalid.',
            'year.required' => 'Manufacturing year is required.',
            'year.integer' => 'Year must be a valid number.',
            'year.min' => 'Year must be 1900 or later.',
            'year.max' => 'Year cannot be in the future.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be at least ₹1,000.',
            'condition.required' => 'Please select car condition.',
            'condition.in' => 'Invalid car condition selected.',
            'mileage.integer' => 'Mileage must be a valid number.',
            'mileage.min' => 'Mileage cannot be negative.',
            'vin.regex' => 'VIN must be a valid 17-character alphanumeric code.',
            'fuel_type.required' => 'Please select fuel type.',
            'fuel_type.in' => 'Invalid fuel type selected.',
            'transmission.required' => 'Please select transmission type.',
            'transmission.in' => 'Invalid transmission type selected.',
            'power.max' => 'Power value seems too high. Please verify.',
            'torque.max' => 'Torque value seems too high. Please verify.',
            'mileage_kmpl.max' => 'Mileage value seems too high. Please verify.',
            'exterior_color.required' => 'Exterior color is required.',
            'seats.required' => 'Number of seats is required.',
            'seats.integer' => 'Seats must be a valid number.',
            'seats.min' => 'Car must have at least 2 seats.',
            'seats.max' => 'Car cannot have more than 15 seats.',
            'doors.required' => 'Number of doors is required.',
            'doors.integer' => 'Doors must be a valid number.',
            'doors.min' => 'Car must have at least 2 doors.',
            'doors.max' => 'Car cannot have more than 6 doors.',
            'owners.required' => 'Number of previous owners is required.',
            'owners.integer' => 'Owners must be a valid number.',
            'owners.min' => 'Number of owners cannot be negative.',
            'owners.max' => 'Number of owners cannot exceed 10.',
            'insurance_valid.required' => 'Please specify if insurance is valid.',
            'under_warranty.required' => 'Please specify if car is under warranty.',
            'insurance_expiry.date' => 'Insurance expiry must be a valid date.',
            'insurance_expiry.after' => 'Insurance expiry date must be in the future.',
            'service_history.max' => 'Service history cannot exceed 1000 characters.',
            'description.max' => 'Description cannot exceed 5000 characters.',
            'images.array' => 'Images must be an array.',
            'images.*.required' => 'Image file is required.',
            'images.*.image' => 'Uploaded file must be an image.',
            'images.*.mimes' => 'Image must be in JPEG, JPG, PNG, or WEBP format.',
            'images.*.max' => 'Each image must not exceed 5MB in size.',
        ]);
    }
}

