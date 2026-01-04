<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    CarController,
    FavoriteController,
    AlertController,
    InquiryController,
    DealerController
};

use App\Http\Controllers\Dealer\{
    DashboardController as DealerDashboardController,
    CarController as DealerCarController
};
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\CarStatusController;



Route::get('/', function () {
    return view('welcome');
});

// Unified Dashboard Routes
Route::prefix('dashboard')->middleware(['auth', 'verified'])->group(function () {
    // Dashboard overview - accessible to all authenticated users
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile route
    Route::get('/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
    
    // CRUD routes - only for dealers and admins (permission checked in controller)
    Route::get('/my-cars', [DashboardController::class, 'myCars'])->name('dashboard.my-cars');
    Route::get('/cars/create', [DashboardController::class, 'createCar'])->name('dashboard.cars.create');
    Route::post('/cars', [DashboardController::class, 'storeCar'])->name('dashboard.cars.store');
    Route::get('/cars/{car:id}/edit', [DashboardController::class, 'editCar'])->name('dashboard.cars.edit');
    Route::put('/cars/{car:id}', [DashboardController::class, 'updateCar'])->name('dashboard.cars.update');
    Route::delete('/cars/{car:id}', [DashboardController::class, 'deleteCar'])->name('dashboard.cars.delete');
});

// Admin Routes - Only for admin role
Route::prefix('admin')->middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // User Management Routes
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::get('/cars', [AdminController::class, 'cars'])->name('cars');
    Route::post('/cars/{car}/approve', [AdminController::class, 'approveCar'])->name('cars.approve');
    Route::post('/cars/{car}/reject', [AdminController::class, 'rejectCar'])->name('cars.reject');
    Route::delete('/cars/{car}', [AdminController::class, 'deleteCar'])->name('cars.delete');
    Route::post('/cars/bulk-approve', [AdminController::class, 'bulkApproveCars'])->name('cars.bulk-approve');
    Route::post('/cars/bulk-reject', [AdminController::class, 'bulkRejectCars'])->name('cars.bulk-reject');
    Route::post('/cars/bulk-delete', [AdminController::class, 'bulkDeleteCars'])->name('cars.bulk-delete');
    
    // Make Management Routes
    Route::get('/makes', [AdminController::class, 'makes'])->name('makes');
    Route::get('/makes/create', [AdminController::class, 'createMake'])->name('makes.create');
    Route::post('/makes', [AdminController::class, 'storeMake'])->name('makes.store');
    Route::get('/makes/{make}/edit', [AdminController::class, 'editMake'])->name('makes.edit');
    Route::put('/makes/{make}', [AdminController::class, 'updateMake'])->name('makes.update');
    Route::delete('/makes/{make}', [AdminController::class, 'deleteMake'])->name('makes.delete');
    
    // Model Management Routes (using carModel to avoid conflict with route model binding)
    Route::get('/car-models', [AdminController::class, 'models'])->name('models');
    Route::get('/car-models/create', [AdminController::class, 'createModel'])->name('models.create');
    Route::post('/car-models', [AdminController::class, 'storeModel'])->name('models.store');
    Route::get('/car-models/{carModel:id}/edit', [AdminController::class, 'editModel'])->name('models.edit');
    Route::put('/car-models/{carModel:id}', [AdminController::class, 'updateModel'])->name('models.update');
    Route::delete('/car-models/{carModel:id}', [AdminController::class, 'deleteModel'])->name('models.delete');
    
    // Admin Color Customization Routes
    Route::get('/color-settings', [AdminController::class, 'colorSettings'])->name('color-settings');
    Route::post('/color-settings', [AdminController::class, 'saveColorSettings'])->name('color-settings.save');
    Route::post('/color-settings/preset', [AdminController::class, 'applyPreset'])->name('color-settings.preset');
    Route::post('/color-settings/reset', [AdminController::class, 'resetColors'])->name('color-settings.reset');
    
    // Features Management Routes
    Route::get('/features', [AdminController::class, 'features'])->name('features');
    Route::get('/features/create', [AdminController::class, 'createFeature'])->name('features.create');
    Route::post('/features', [AdminController::class, 'storeFeature'])->name('features.store');
    Route::get('/features/{feature}/edit', [AdminController::class, 'editFeature'])->name('features.edit');
    Route::put('/features/{feature}', [AdminController::class, 'updateFeature'])->name('features.update');
    Route::delete('/features/{feature}', [AdminController::class, 'deleteFeature'])->name('features.delete');
    
    // Safety Features Management Routes
    Route::get('/safety-features', [AdminController::class, 'safetyFeatures'])->name('safety-features');
    Route::get('/safety-features/create', [AdminController::class, 'createSafetyFeature'])->name('safety-features.create');
    Route::post('/safety-features', [AdminController::class, 'storeSafetyFeature'])->name('safety-features.store');
    Route::get('/safety-features/{safetyFeature}/edit', [AdminController::class, 'editSafetyFeature'])->name('safety-features.edit');
    Route::put('/safety-features/{safetyFeature}', [AdminController::class, 'updateSafetyFeature'])->name('safety-features.update');
    Route::delete('/safety-features/{safetyFeature}', [AdminController::class, 'deleteSafetyFeature'])->name('safety-features.delete');
    
    // Cities Management Routes
    Route::get('/cities', [AdminController::class, 'cities'])->name('cities');
    Route::get('/cities/create', [AdminController::class, 'createCity'])->name('cities.create');
    Route::post('/cities', [AdminController::class, 'storeCity'])->name('cities.store');
    Route::get('/cities/{city}/edit', [AdminController::class, 'editCity'])->name('cities.edit');
    Route::put('/cities/{city}', [AdminController::class, 'updateCity'])->name('cities.update');
    Route::delete('/cities/{city}', [AdminController::class, 'deleteCity'])->name('cities.delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // API Routes for real-time updates
    Route::get('/api/cars/status-updates', [CarStatusController::class, 'getStatusUpdates'])->name('api.cars.status-updates');
});


// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Cars
Route::prefix('cars')->name('cars.')->group(function () {
    Route::get('/', [CarController::class, 'index'])->name('index');
    Route::get('/compare', [CarController::class, 'compare'])->name('compare');
    Route::get('/ajax/models', [CarController::class, 'getModels'])->name('models'); // Must be before {car:slug} route
    Route::get('/{car:slug}', [CarController::class, 'show'])->name('show');
});


// Inquiries
Route::post('/cars/{car}/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');

// Dealers Public Profile
Route::get('/dealers/{dealer:slug}', [DealerController::class, 'show'])->name('dealers.show');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Favorites
    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [FavoriteController::class, 'index'])->name('index');
        Route::post('/{car}', [FavoriteController::class, 'store'])->name('store');
        Route::delete('/{car}', [FavoriteController::class, 'destroy'])->name('destroy');
    });

    // Alerts
    Route::resource('alerts', AlertController::class)->except(['show', 'edit', 'update']);
    Route::post('/alerts/{alert}/toggle', [AlertController::class, 'toggle'])->name('alerts.toggle');
});


// Dealer Routes
Route::prefix('dealer')->name('dealer.')->middleware(['auth', 'dealer'])->group(function () {
    Route::get('/dashboard', [DealerDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('cars', DealerCarController::class);
});












require __DIR__.'/auth.php';
