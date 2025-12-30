@extends('layouts.app')

@section('banner')
<!-- Hero Banner with Search -->
<div class="hero-banner bg-primary text-white py-5" >
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold mb-3">Find Your Perfect Car</h1>
                <p class="lead mb-4">Browse thousands of new and used cars from verified dealers across India</p>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Search Cars</h5>
                        <form action="{{ route('cars.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Make</label>
                                    <select class="form-select" name="make_id">
                                        <option value="">All Makes</option>
                                        @foreach($popularMakes as $make)
                                            <option value="{{ $make->id }}">{{ $make->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <select class="form-select" name="city_id">
                                        <option value="">All Cities</option>
                                        @foreach($popularCities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Price Range</label>
                                    <select class="form-select" name="max_price">
                                        <option value="">Any Price</option>
                                        <option value="500000">Under ₹5 Lakh</option>
                                        <option value="1000000">Under ₹10 Lakh</option>
                                        <option value="2000000">Under ₹20 Lakh</option>
                                        <option value="5000000">Under ₹50 Lakh</option>
                                        <option value="">Above ₹50 Lakh</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Condition</label>
                                    <select class="form-select" name="condition">
                                        <option value="">All</option>
                                        <option value="new">New</option>
                                        <option value="used">Used</option>
                                        <option value="certified">Certified</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                                        <i class="fas fa-search me-2"></i>Search Cars
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<!-- Popular Car Makes -->
@if($popularMakes->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Popular Car Brands</h2>
            <a href="{{ route('cars.index') }}" class="text-decoration-none">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            @foreach($popularMakes->take(8) as $make)
                <div class="col-6 col-md-3 col-lg-2">
                    <a href="{{ route('cars.index', ['make_id' => $make->id]) }}" class="text-decoration-none text-dark">
                        <div class="card h-100 text-center p-3 city-card">
                            @if($make->logo)
                                <img src="{{ asset('storage/' . $make->logo) }}" alt="{{ $make->name }}" class="make-logo mb-2">
                            @else
                                <div class="make-logo mb-2 d-flex align-items-center justify-content-center bg-light rounded">
                                    <i class="fas fa-car fa-2x text-muted"></i>
                                </div>
                            @endif
                            <h6 class="mb-0">{{ $make->name }}</h6>
                            <small class="text-muted">{{ $make->cars_count ?? 0 }} cars</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Cars -->
@if($featuredCars->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Featured Cars</h2>
            <a href="{{ route('cars.index', ['sort' => 'featured']) }}" class="text-decoration-none">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            @foreach($featuredCars as $car)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        @php
                            $image = $car->getFirstMediaUrl('images') ?: 'https://via.placeholder.com/300x200?text=No+Image';
                        @endphp
                        <div class="position-relative">
                            <img src="{{ $image }}" class="card-img-top" alt="{{ $car->title }}">
                            @if($car->is_featured)
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">Featured</span>
                            @endif
                            @if($car->is_verified)
                                <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                    <i class="fas fa-check-circle"></i> Verified
                                </span>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="card-title mb-2">{{ $car->make->name }} {{ $car->model->name }}</h5>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-calendar-alt"></i> {{ $car->year }} 
                                <span class="mx-2">|</span>
                                <i class="fas fa-tachometer-alt"></i> {{ number_format($car->mileage) }} km
                            </p>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt"></i> {{ $car->city->name }}, {{ $car->city->state }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price-tag">₹{{ number_format($car->price, 0) }}</span>
                                <span class="badge bg-info">{{ ucfirst($car->condition) }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('cars.show', $car->slug) }}" class="btn btn-primary btn-sm flex-fill">
                                    View Details
                                </a>
                                @auth
                                    <form action="{{ route('favorites.store', $car->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </form>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- New Cars -->
@if($newCars->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">New Cars</h2>
            <a href="{{ route('cars.index', ['condition' => 'new']) }}" class="text-decoration-none">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            @foreach($newCars as $car)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        @php
                            $image = $car->getFirstMediaUrl('images') ?: 'https://via.placeholder.com/300x200?text=No+Image';
                        @endphp
                        <img src="{{ $image }}" class="card-img-top" alt="{{ $car->title }}">
                        <div class="card-body">
                            <h5 class="card-title mb-2">{{ $car->make->name }} {{ $car->model->name }}</h5>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-calendar-alt"></i> {{ $car->year }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-map-marker-alt"></i> {{ $car->city->name }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price-tag">₹{{ number_format($car->price, 0) }}</span>
                                <span class="badge bg-success">New</span>
                            </div>
                            <a href="{{ route('cars.show', $car->slug) }}" class="btn btn-primary btn-sm w-100">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Used Cars Section -->
@if(isset($usedCars) && $usedCars->count() > 0)
<section class="py-5" style="background: #fff;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="font-size: 2rem; color: #333;">Used Cars</h2>
            <a href="{{ route('cars.index', ['condition' => 'used']) }}" class="text-decoration-none fw-bold" style="color: #2271b1; font-size: 1rem;">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">
            @foreach($usedCars as $car)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm" style="border: none; border-radius: 8px; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease;" 
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)'"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'">
                        @php
                            $image = $car->getFirstMediaUrl('images') ?: 'https://via.placeholder.com/400x250?text=' . urlencode($car->title);
                        @endphp
                        <div style="position: relative; width: 100%; height: 200px; overflow: hidden; background: #f5f5f5;">
                            <img src="{{ $image }}" 
                                 alt="{{ $car->title }}" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                            <span class="badge" style="position: absolute; top: 10px; right: 10px; background: #ffc107; color: #000; padding: 5px 10px; font-weight: 600; border-radius: 4px;">
                                Used
                            </span>
                        </div>
                        <div class="card-body" style="padding: 20px;">
                            <h5 class="card-title mb-3" style="font-size: 1.25rem; font-weight: 600; color: #333; margin-bottom: 10px;">
                                {{ $car->make->name ?? 'N/A' }} {{ $car->model->name ?? 'N/A' }}
                            </h5>
                            <div class="mb-3" style="display: flex; flex-wrap: wrap; gap: 10px; color: #666; font-size: 0.9rem;">
                                <span><i class="fas fa-calendar-alt" style="margin-right: 5px;"></i>{{ $car->year ?? 'N/A' }}</span>
                                <span><i class="fas fa-tachometer-alt" style="margin-right: 5px;"></i>{{ $car->mileage ? number_format($car->mileage) . ' km' : 'N/A' }}</span>
                                <span><i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i>{{ $car->city->name ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3" style="flex-wrap: wrap; gap: 10px;">
                                <span style="font-size: 1.5rem; font-weight: 700; color: #ff6b35;">
                                    ₹{{ number_format($car->price, 0) }}
                                </span>
                            </div>
                            <a href="{{ route('cars.show', $car->slug) }}" 
                               class="btn w-100" 
                               style="background: #ff6b35; color: white; border: none; padding: 12px; font-weight: 600; border-radius: 6px; text-decoration: none; display: block; text-align: center; transition: background 0.3s ease;"
                               onmouseover="this.style.background='#e55a2b'"
                               onmouseout="this.style.background='#ff6b35'">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Popular Cities -->
@if($popularCities->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Popular Cities</h2>
            <a href="{{ route('cars.index') }}" class="text-decoration-none">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            @foreach($popularCities as $city)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('cars.index', ['city_id' => $city->id]) }}" class="text-decoration-none text-dark">
                        <div class="card city-card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                                <h5 class="mb-1">{{ $city->name }}</h5>
                                <p class="text-muted small mb-0">{{ $city->state }}</p>
                                <span class="badge bg-primary mt-2">{{ $city->cars_count ?? 0 }} cars</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Why Choose Us -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Why Choose CarMarketplace?</h2>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-shield-alt fa-3x text-primary"></i>
                </div>
                <h5>Verified Listings</h5>
                <p class="text-muted">All cars are verified by our team to ensure quality and authenticity.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-users fa-3x text-primary"></i>
                </div>
                <h5>Trusted Dealers</h5>
                <p class="text-muted">Connect with verified and trusted car dealers across India.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-search fa-3x text-primary"></i>
                </div>
                <h5>Easy Search</h5>
                <p class="text-muted">Find your perfect car with our advanced search and filter options.</p>
            </div>
        </div>
    </div>
</section>
@endsection
