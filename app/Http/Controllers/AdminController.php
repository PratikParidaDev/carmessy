<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Car;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
}

