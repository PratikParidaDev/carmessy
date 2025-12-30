<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Car;
use App\Models\Dealer;
use App\Models\Make;
use App\Models\CarModel;
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
        $pendingCars = Car::with(['make', 'model', 'dealer.user'])
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
    public function cars()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $cars = Car::with(['make', 'model', 'city', 'dealer.user', 'media'])
            ->latest()
            ->paginate(20);

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
        ]);

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
}

