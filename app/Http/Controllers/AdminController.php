<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Car;
use App\Models\Dealer;
use App\Models\Make;
use App\Models\CarModel;
use App\Models\AdminPreference;
use App\Models\Feature;
use App\Models\SafetyFeature;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Get all stats
        $stats = [
            'total_users' => User::count(),
            'total_dealers' => User::where('role', 'dealer')->count(),
            'total_buyers' => User::where('role', 'buyer')->count(),
            'total_cars' => Car::count(),
            'pending_cars' => Car::where('status', 'pending')->count(),
            'approved_cars' => Car::where('status', 'approved')->count(),
            'rejected_cars' => Car::where('status', 'rejected')->count(),
            'sold_cars' => Car::where('status', 'sold')->count(),
        ];

        // Recent pending cars
        $pendingCars = Car::with(['make', 'model', 'dealer.user', 'media'])
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        // Recent users
        $recentUsers = User::latest()
            ->limit(5)
            ->get();

        return view('dashboard', [
            'section' => 'admin-dashboard',
            'stats' => $stats,
            'pendingCars' => $pendingCars,
            'recentUsers' => $recentUsers,
        ]);
    }

    /**
     * List all users
     */
    public function users()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $users = User::with('dealer')
            ->latest()
            ->paginate(20);

        return view('dashboard', [
            'section' => 'admin-users',
            'users' => $users,
        ]);
    }

    /**
     * Show form to create a new user
     */
    public function createUser()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('dashboard', [
            'section' => 'admin-user-create',
        ]);
    }

    /**
     * Store a newly created user
     */
    public function storeUser(Request $request)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:user,dealer,buyer'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show form to edit a user
     */
    public function editUser(User $user)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('dashboard', [
            'section' => 'admin-user-edit',
            'editUser' => $user,
        ]);
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, User $user)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:user,dealer,buyer,admin'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user
     */
    public function deleteUser(User $user)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Prevent admin from deleting themselves
        if ($user->id === $admin->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        DB::beginTransaction();
        try {
            // Delete user's cars
            Car::where('dealer_id', $user->dealer->id ?? null)->delete();
            
            // Delete user's dealer profile if exists
            if ($user->dealer) {
                $user->dealer->delete();
            }

            // Delete user
            $user->delete();

            DB::commit();

            return back()->with('success', 'User and all associated data deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * List all cars (admin view)
     */
    public function cars(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $query = Car::with(['make', 'model', 'city', 'dealer.user', 'media']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhereHas('make', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                  ->orWhereHas('model', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                  ->orWhereHas('dealer.user', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $cars = $query->latest()->paginate(20)->withQueryString();

        return view('dashboard', [
            'section' => 'admin-cars',
            'cars' => $cars,
        ]);
    }

    /**
     * Approve a car
     */
    public function approveCar(Car $car)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $car->update([
            'status' => 'approved',
            'published_at' => now(),
        ]);

        // Clear cache
        \Illuminate\Support\Facades\Cache::forget("car:show:{$car->id}");
        \Illuminate\Support\Facades\Cache::forget('homepage:data');
        // Broadcast status update for real-time updates
        event(new \App\Events\CarStatusUpdated($car->fresh()));

        return back()->with('success', 'Car approved successfully.');
    }

    /**
     * Reject a car
     */
    public function rejectCar(Car $car)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $car->update([
            'status' => 'rejected',
            'published_at' => null, // Clear published_at when rejected
        ]);

        // Clear cache
        \Illuminate\Support\Facades\Cache::forget("car:show:{$car->id}");
        \Illuminate\Support\Facades\Cache::forget('homepage:data');
        // Broadcast status update for real-time updates
        event(new \App\Events\CarStatusUpdated($car->fresh()));

        return back()->with('success', 'Car rejected successfully.');
    }

    /**
     * Delete a car (admin)
     */
    public function deleteCar(Car $car)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        try {
            // Delete all media
            $car->clearMediaCollection('images');
            
            // Delete car
            $car->delete();

            return back()->with('success', 'Car deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete car: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve cars
     */
    public function bulkApproveCars(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $carIds = $request->input('car_ids', []);
        
        if (empty($carIds)) {
            return back()->with('error', 'No cars selected.');
        }

        Car::whereIn('id', $carIds)->update([
            'status' => 'approved',
            'published_at' => now(),
        ]);

        return back()->with('success', count($carIds) . ' cars approved successfully.');
    }

    /**
     * Bulk reject cars
     */
    public function bulkRejectCars(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $carIds = $request->input('car_ids', []);
        
        if (empty($carIds)) {
            return back()->with('error', 'No cars selected.');
        }

        Car::whereIn('id', $carIds)->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', count($carIds) . ' cars rejected successfully.');
    }

    /**
     * Bulk delete cars
     */
    public function bulkDeleteCars(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $carIds = $request->input('car_ids', []);
        
        if (empty($carIds)) {
            return back()->with('error', 'No cars selected.');
        }

        DB::beginTransaction();
        try {
            $cars = Car::whereIn('id', $carIds)->get();
            
            foreach ($cars as $car) {
                $car->clearMediaCollection('images');
                $car->delete();
            }

            DB::commit();

            return back()->with('success', count($carIds) . ' cars deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete cars: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * MAKE MANAGEMENT
     * ============================================
     */

    /**
     * List all makes
     */
    public function makes()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $makes = Make::withCount('models')
            ->withCount('cars')
            ->latest()
            ->paginate(20);

        return view('dashboard', [
            'section' => 'admin-makes',
            'makes' => $makes,
        ]);
    }

    /**
     * Show form to create a new make
     */
    public function createMake()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('dashboard', [
            'section' => 'admin-make-create',
        ]);
    }

    /**
     * Store a newly created make
     */
    public function storeMake(Request $request)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:makes,name'],
            'country' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'is_popular' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $make = Make::create([
            'name' => $validated['name'],
            'country' => $validated['country'] ?? null,
            'is_popular' => $request->has('is_popular') ? true : false,
            'is_active' => $request->has('is_active') ? true : true,
            'order' => $validated['order'] ?? 0,
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('makes/logos', 'public');
            $make->logo = $logoPath;
            $make->save();
        }

        return redirect()->route('admin.makes')
            ->with('success', 'Make created successfully.');
    }

    /**
     * Show form to edit a make
     */
    public function editMake(Make $make)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('dashboard', [
            'section' => 'admin-make-edit',
            'editMake' => $make,
        ]);
    }

    /**
     * Update a make
     */
    public function updateMake(Request $request, Make $make)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:makes,name,' . $make->id],
            'country' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'is_popular' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $make->name = $validated['name'];
        $make->country = $validated['country'] ?? null;
        $make->is_popular = $request->has('is_popular') ? true : false;
        $make->is_active = $request->has('is_active') ? true : true;
        $make->order = $validated['order'] ?? 0;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($make->logo && Storage::disk('public')->exists($make->logo)) {
                Storage::disk('public')->delete($make->logo);
            }
            $logoPath = $request->file('logo')->store('makes/logos', 'public');
            $make->logo = $logoPath;
        }

        $make->save();

        return redirect()->route('admin.makes')
            ->with('success', 'Make updated successfully.');
    }

    /**
     * Delete a make
     */
    public function deleteMake(Make $make)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Check if make has cars or models
        if ($make->cars()->count() > 0 || $make->models()->count() > 0) {
            return back()->with('error', 'Cannot delete make. It has associated models or cars. Please delete them first.');
        }

        // Delete logo if exists
        if ($make->logo && Storage::disk('public')->exists($make->logo)) {
            Storage::disk('public')->delete($make->logo);
        }

        $make->delete();

        return back()->with('success', 'Make deleted successfully.');
    }

    /**
     * ============================================
     * MODEL MANAGEMENT
     * ============================================
     */

    /**
     * List all models
     */
    public function models(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $models = CarModel::with('make')
            ->withCount('cars')
            ->when($request->make_id, fn($q) => $q->where('make_id', $request->make_id))
            ->latest()
            ->paginate(20);

        $makes = Make::where('is_active', true)->orderBy('name')->get();

        return view('dashboard', [
            'section' => 'admin-models',
            'models' => $models,
            'makes' => $makes,
            'currentMakeId' => $request->make_id,
        ]);
    }

    /**
     * Show form to create a new model
     */
    public function createModel()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $makes = Make::where('is_active', true)->orderBy('name')->get();

        return view('dashboard', [
            'section' => 'admin-model-create',
            'makes' => $makes,
        ]);
    }

    /**
     * Store a newly created model
     */
    public function storeModel(Request $request)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'make_id' => ['required', 'exists:makes,id'],
            'name' => ['required', 'string', 'max:255'],
            'body_type' => ['nullable', 'in:sedan,suv,hatchback,coupe,convertible,wagon,van,truck,luxury'],
            'year_start' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'year_end' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 10), 'gte:year_start'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Check for duplicate model name for the same make
        $existingModel = CarModel::where('make_id', $validated['make_id'])
            ->where('name', $validated['name'])
            ->first();

        if ($existingModel) {
            return back()->withInput()->withErrors(['name' => 'This model already exists for the selected make.']);
        }

        $model = CarModel::create([
            'make_id' => $validated['make_id'],
            'name' => $validated['name'],
            'body_type' => $validated['body_type'] ?? null,
            'year_start' => $validated['year_start'] ?? null,
            'year_end' => $validated['year_end'] ?? null,
            'is_active' => $request->has('is_active') ? true : true,
        ]);

        return redirect()->route('admin.models')
            ->with('success', 'Model created successfully.');
    }

    /**
     * Show form to edit a model
     */
    public function editModel(CarModel $carModel)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $makes = Make::where('is_active', true)->orderBy('name')->get();

        return view('dashboard', [
            'section' => 'admin-model-edit',
            'editModel' => $carModel,
            'makes' => $makes,
        ]);
    }

    /**
     * Update a model
     */
    public function updateModel(Request $request, CarModel $carModel)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'make_id' => ['required', 'exists:makes,id'],
            'name' => ['required', 'string', 'max:255'],
            'body_type' => ['nullable', 'in:sedan,suv,hatchback,coupe,convertible,wagon,van,truck,luxury'],
            'year_start' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'year_end' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 10), 'gte:year_start'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Check for duplicate model name for the same make (excluding current model)
        $existingModel = CarModel::where('make_id', $validated['make_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $carModel->id)
            ->first();

        if ($existingModel) {
            return back()->withInput()->withErrors(['name' => 'This model already exists for the selected make.']);
        }

        $carModel->make_id = $validated['make_id'];
        $carModel->name = $validated['name'];
        $carModel->body_type = $validated['body_type'] ?? null;
        $carModel->year_start = $validated['year_start'] ?? null;
        $carModel->year_end = $validated['year_end'] ?? null;
        $carModel->is_active = $request->has('is_active') ? true : true;
        $carModel->save();

        return redirect()->route('admin.models')
            ->with('success', 'Model updated successfully.');
    }

    /**
     * Delete a model
     */
    public function deleteModel(CarModel $carModel)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Check if model has cars
        if ($carModel->cars()->count() > 0) {
            return back()->with('error', 'Cannot delete model. It has associated cars. Please delete them first.');
        }

        $carModel->delete();

        return back()->with('success', 'Model deleted successfully.');
    }

    /**
     * ============================================
     * ADMIN COLOR CUSTOMIZATION
     * ============================================
     */

    /**
     * Show admin color customization page
     */
    public function colorSettings()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $preference = $user->adminPreference;
        $presets = AdminPreference::getPresets();
        $currentColors = $user->getAdminColors();

        return view('dashboard', [
            'section' => 'admin-color-settings',
            'preference' => $preference,
            'presets' => $presets,
            'currentColors' => $currentColors,
        ]);
    }

    /**
     * Save admin color preferences
     */
    public function saveColorSettings(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'sidebar_bg' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sidebar_hover' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sidebar_text' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sidebar_active' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'content_bg' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'primary_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $preference = AdminPreference::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('admin.color-settings')
            ->with('success', 'Color scheme saved successfully! The changes will be applied immediately.');
    }

    /**
     * Apply a preset color scheme
     */
    public function applyPreset(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $presetName = $request->input('preset');
        $presets = AdminPreference::getPresets();

        if (!isset($presets[$presetName])) {
            return back()->with('error', 'Invalid preset selected.');
        }

        $preset = $presets[$presetName];

        AdminPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'sidebar_bg' => $preset['sidebar_bg'],
                'sidebar_hover' => $preset['sidebar_hover'],
                'sidebar_text' => $preset['sidebar_text'],
                'sidebar_active' => $preset['sidebar_active'],
                'content_bg' => $preset['content_bg'],
                'primary_color' => $preset['primary_color'],
            ]
        );

        return redirect()->route('admin.color-settings')
            ->with('success', 'Color preset "' . $preset['name'] . '" applied successfully!');
    }

    /**
     * Reset to default colors
     */
    public function resetColors()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        if ($user->adminPreference) {
            $user->adminPreference->delete();
        }

        return redirect()->route('admin.color-settings')
            ->with('success', 'Color scheme reset to default successfully!');
    }

    /**
     * ============================================
     * FEATURES MANAGEMENT
     * ============================================
     */

    /**
     * List all features
     */
    public function features()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $features = Feature::orderBy('order')->orderBy('name')->paginate(20);

        return view('dashboard', [
            'section' => 'admin-features',
            'features' => $features,
        ]);
    }

    /**
     * Show form to create a feature
     */
    public function createFeature()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('dashboard', [
            'section' => 'admin-feature-create',
        ]);
    }

    /**
     * Store a new feature
     */
    public function storeFeature(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:features,name',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Feature::create($validated);

        return redirect()->route('admin.features')
            ->with('success', 'Feature created successfully.');
    }

    /**
     * Show form to edit a feature
     */
    public function editFeature(Feature $feature)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('dashboard', [
            'section' => 'admin-feature-edit',
            'editFeature' => $feature,
        ]);
    }

    /**
     * Update a feature
     */
    public function updateFeature(Request $request, Feature $feature)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:features,name,' . $feature->id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $feature->update($validated);

        return redirect()->route('admin.features')
            ->with('success', 'Feature updated successfully.');
    }

    /**
     * Delete a feature
     */
    public function deleteFeature(Feature $feature)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $feature->delete();

        return back()->with('success', 'Feature deleted successfully.');
    }

    /**
     * ============================================
     * SAFETY FEATURES MANAGEMENT
     * ============================================
     */

    /**
     * List all safety features
     */
    public function safetyFeatures()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $safetyFeatures = SafetyFeature::orderBy('order')->orderBy('name')->paginate(20);

        return view('dashboard', [
            'section' => 'admin-safety-features',
            'safetyFeatures' => $safetyFeatures,
        ]);
    }

    /**
     * Show form to create a safety feature
     */
    public function createSafetyFeature()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('dashboard', [
            'section' => 'admin-safety-feature-create',
        ]);
    }

    /**
     * Store a new safety feature
     */
    public function storeSafetyFeature(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:safety_features,name',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        SafetyFeature::create($validated);

        return redirect()->route('admin.safety-features')
            ->with('success', 'Safety feature created successfully.');
    }

    /**
     * Show form to edit a safety feature
     */
    public function editSafetyFeature(SafetyFeature $safetyFeature)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('dashboard', [
            'section' => 'admin-safety-feature-edit',
            'editSafetyFeature' => $safetyFeature,
        ]);
    }

    /**
     * Update a safety feature
     */
    public function updateSafetyFeature(Request $request, SafetyFeature $safetyFeature)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:safety_features,name,' . $safetyFeature->id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $safetyFeature->update($validated);

        return redirect()->route('admin.safety-features')
            ->with('success', 'Safety feature updated successfully.');
    }

    /**
     * Delete a safety feature
     */
    public function deleteSafetyFeature(SafetyFeature $safetyFeature)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $safetyFeature->delete();

        return back()->with('success', 'Safety feature deleted successfully.');
    }

    /**
     * ============================================
     * CITIES MANAGEMENT
     * ============================================
     */

    /**
     * List all cities
     */
    public function cities()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $cities = City::orderBy('state')->orderBy('name')->paginate(20);

        return view('dashboard', [
            'section' => 'admin-cities',
            'cities' => $cities,
        ]);
    }

    /**
     * Show form to create a city
     */
    public function createCity()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('dashboard', [
            'section' => 'admin-city-create',
        ]);
    }

    /**
     * Store a new city
     */
    public function storeCity(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
            'state' => 'required|string|max:255',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
        ]);

        City::create($validated);

        return redirect()->route('admin.cities')
            ->with('success', 'City created successfully.');
    }

    /**
     * Show form to edit a city
     */
    public function editCity(City $city)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('dashboard', [
            'section' => 'admin-city-edit',
            'editCity' => $city,
        ]);
    }

    /**
     * Update a city
     */
    public function updateCity(Request $request, City $city)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cities,name,' . $city->id,
            'state' => 'required|string|max:255',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $city->update($validated);

        return redirect()->route('admin.cities')
            ->with('success', 'City updated successfully.');
    }

    /**
     * Delete a city
     */
    public function deleteCity(City $city)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Check if city has cars or dealers
        if ($city->cars()->count() > 0 || $city->dealers()->count() > 0) {
            return back()->with('error', 'Cannot delete city. It has associated cars or dealers.');
        }

        $city->delete();

        return back()->with('success', 'City deleted successfully.');
    }

    /**
     * ============================================
     * SUPER ADMIN - ADMIN MANAGEMENT
     * ============================================
     */

    /**
     * List all admins (super admin only)
     */
    public function admins()
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        $admins = User::whereIn('role', ['admin', 'super_admin'])
            ->latest()
            ->paginate(20);

        return view('dashboard', [
            'section' => 'super-admin-admins',
            'admins' => $admins,
        ]);
    }

    /**
     * Show form to create a new admin
     */
    public function createAdmin()
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        return view('dashboard', [
            'section' => 'super-admin-admin-create',
        ]);
    }

    /**
     * Store a newly created admin
     */
    public function storeAdmin(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,super_admin'],
        ]);

        $admin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        return redirect()->route('super-admin.admins')
            ->with('success', ucfirst(str_replace('_', ' ', $validated['role'])) . ' created successfully.');
    }

    /**
     * Show form to edit an admin
     */
    public function editAdmin(User $admin)
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        return view('dashboard', [
            'section' => 'super-admin-admin-edit',
            'editAdmin' => $admin,
        ]);
    }

    /**
     * Update an admin
     */
    public function updateAdmin(Request $request, User $admin)
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,super_admin'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $admin->update($updateData);

        return redirect()->route('super-admin.admins')
            ->with('success', 'Admin updated successfully.');
    }

    /**
     * Delete an admin (super admin only)
     */
    public function deleteAdmin(User $admin)
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        // Prevent deleting yourself
        if ($admin->id === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return back()->with('success', 'Admin deleted successfully.');
    }
}

