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



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Cars
Route::prefix('cars')->name('cars.')->group(function () {
    Route::get('/', [CarController::class, 'index'])->name('index');
    Route::get('/compare', [CarController::class, 'compare'])->name('compare');
    Route::get('/{car:slug}', [CarController::class, 'show'])->name('show');
    Route::get('/ajax/models', [CarController::class, 'getModels'])->name('models');
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
