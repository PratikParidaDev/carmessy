@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="container">
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Cars</li>
            </ol>
        </nav>

        <!-- Search Bar -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('cars.index') }}" method="GET" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Make</label>
                            <select class="form-select form-select-sm" name="make_id" id="makeSelect">
                                <option value="">All Makes</option>
                                @foreach($makes as $make)
                                    <option value="{{ $make->id }}" {{ request('make_id') == $make->id ? 'selected' : '' }}>
                                        {{ $make->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Model</label>
                            <select class="form-select form-select-sm" name="model_id" id="modelSelect">
                                <option value="">All Models</option>
                                @if(request('make_id'))
                                    @php
                                        $selectedMake = $makes->firstWhere('id', request('make_id'));
                                        $models = $selectedMake ? $selectedMake->models()->where('is_active', true)->orderBy('name')->get() : collect();
                                    @endphp
                                    @foreach($models as $model)
                                        <option value="{{ $model->id }}" {{ request('model_id') == $model->id ? 'selected' : '' }}>
                                            {{ $model->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">City</label>
                            <select class="form-select form-select-sm" name="city_id">
                                <option value="">All Cities</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Price</label>
                            <select class="form-select form-select-sm" name="max_price">
                                <option value="">Any Price</option>
                                <option value="500000" {{ request('max_price') == '500000' ? 'selected' : '' }}>Under ₹5 Lakh</option>
                                <option value="1000000" {{ request('max_price') == '1000000' ? 'selected' : '' }}>Under ₹10 Lakh</option>
                                <option value="2000000" {{ request('max_price') == '2000000' ? 'selected' : '' }}>Under ₹20 Lakh</option>
                                <option value="5000000" {{ request('max_price') == '5000000' ? 'selected' : '' }}>Under ₹50 Lakh</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('cars.index') }}" method="GET" id="filterForm">
                            <!-- Preserve search query -->
                            @if(request('q'))
                                <input type="hidden" name="q" value="{{ request('q') }}">
                            @endif

                            <!-- Condition -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Condition</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="condition" id="condition_all" value="" {{ !request('condition') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_all">All</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="condition" id="condition_new" value="new" {{ request('condition') == 'new' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_new">New</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="condition" id="condition_used" value="used" {{ request('condition') == 'used' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_used">Used</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="condition" id="condition_certified" value="certified" {{ request('condition') == 'certified' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_certified">Certified</label>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Price Range (₹)</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" name="min_price" placeholder="Min" value="{{ request('min_price') }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" name="max_price" placeholder="Max" value="{{ request('max_price') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Year Range -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Year</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <select class="form-select form-select-sm" name="min_year">
                                            <option value="">Min Year</option>
                                            @for($year = date('Y'); $year >= 2000; $year--)
                                                <option value="{{ $year }}" {{ request('min_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <select class="form-select form-select-sm" name="max_year">
                                            <option value="">Max Year</option>
                                            @for($year = date('Y'); $year >= 2000; $year--)
                                                <option value="{{ $year }}" {{ request('max_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Fuel Type -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Fuel Type</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="fuel_type" id="fuel_all" value="" {{ !request('fuel_type') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fuel_all">All</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="fuel_type" id="fuel_petrol" value="petrol" {{ request('fuel_type') == 'petrol' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fuel_petrol">Petrol</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="fuel_type" id="fuel_diesel" value="diesel" {{ request('fuel_type') == 'diesel' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fuel_diesel">Diesel</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="fuel_type" id="fuel_electric" value="electric" {{ request('fuel_type') == 'electric' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fuel_electric">Electric</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="fuel_type" id="fuel_hybrid" value="hybrid" {{ request('fuel_type') == 'hybrid' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fuel_hybrid">Hybrid</label>
                                </div>
                            </div>

                            <!-- Transmission -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Transmission</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="transmission" id="transmission_all" value="" {{ !request('transmission') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="transmission_all">All</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="transmission" id="transmission_manual" value="manual" {{ request('transmission') == 'manual' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="transmission_manual">Manual</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="transmission" id="transmission_automatic" value="automatic" {{ request('transmission') == 'automatic' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="transmission_automatic">Automatic</label>
                                </div>
                            </div>

                            <!-- Preserve other filters -->
                            @if(request('make_id'))
                                <input type="hidden" name="make_id" value="{{ request('make_id') }}">
                            @endif
                            @if(request('model_id'))
                                <input type="hidden" name="model_id" value="{{ request('model_id') }}">
                            @endif
                            @if(request('city_id'))
                                <input type="hidden" name="city_id" value="{{ request('city_id') }}">
                            @endif

                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('cars.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Results Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0">Found {{ $cars->total() }} Cars</h4>
                        @if(request()->anyFilled(['make_id', 'model_id', 'city_id', 'condition', 'fuel_type', 'transmission', 'min_price', 'max_price']))
                            <small class="text-muted">Filtered results</small>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small mb-0">Sort by:</label>
                        <form action="{{ route('cars.index') }}" method="GET" id="sortForm" class="d-inline">
                            @foreach(request()->except('sort', 'page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <select class="form-select form-select-sm" name="sort" onchange="document.getElementById('sortForm').submit();" style="width: auto;">
                                <option value="">Latest First</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="year_new" {{ request('sort') == 'year_new' ? 'selected' : '' }}>Year: Newest First</option>
                                <option value="mileage_low" {{ request('sort') == 'mileage_low' ? 'selected' : '' }}>Mileage: Low to High</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Car Listings -->
                @if($cars->count() > 0)
                    <div class="row g-4">
                        @foreach($cars as $car)
                            <div class="col-12">
                                <div class="card shadow-sm h-100 car-listing-card">
                                    <div class="row g-0">
                                        <!-- Car Image -->
                                        <div class="col-md-4">
                                            <div class="position-relative">
                                                @php
                                                    $firstMedia = $car->getFirstMedia('images');
                                                    if ($firstMedia) {
                                                        $image = asset('storage/' . $firstMedia->id . '/' . $firstMedia->file_name);
                                                    } else {
                                                        $image = 'https://via.placeholder.com/400x300?text=No+Image';
                                                    }
                                                @endphp
                                                <img src="{{ $image }}" class="img-fluid rounded-start car-listing-image" alt="{{ $car->title }}" style="height: 250px; width: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=No+Image';">
                                                @if($car->is_featured)
                                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                                        <i class="fas fa-star"></i> Featured
                                                    </span>
                                                @endif
                                                @if($car->is_verified)
                                                    <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                                        <i class="fas fa-check-circle"></i> Verified
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Car Details -->
                                        <div class="col-md-8">
                                            <div class="card-body h-100 d-flex flex-column">
                                                <div class="flex-grow-1">
                                                    <h5 class="card-title mb-2">
                                                        <a href="{{ route('cars.show', $car->slug) }}" class="text-decoration-none text-dark">
                                                            {{ $car->make->name }} {{ $car->model->name }}
                                                        </a>
                                                    </h5>
                                                    <p class="text-muted small mb-2">
                                                        <span class="badge bg-info me-2">{{ ucfirst($car->condition) }}</span>
                                                        <i class="fas fa-calendar-alt me-1"></i> {{ $car->year }}
                                                        <span class="mx-2">|</span>
                                                        <i class="fas fa-tachometer-alt me-1"></i> {{ number_format($car->mileage) }} km
                                                    </p>
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">
                                                                <i class="fas fa-gas-pump me-1"></i> {{ ucfirst($car->fuel_type) }}
                                                            </small>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">
                                                                <i class="fas fa-cog me-1"></i> {{ ucfirst($car->transmission) }}
                                                            </small>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">
                                                                <i class="fas fa-map-marker-alt me-1"></i> {{ $car->city->name }}
                                                            </small>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted d-block">
                                                                <i class="fas fa-user me-1"></i> {{ $car->owners }} Owner{{ $car->owners > 1 ? 's' : '' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                                    <div>
                                                        <h4 class="price-tag mb-0">₹{{ number_format($car->price, 0) }}</h4>
                                                        @if($car->dealer)
                                                            <small class="text-muted">by {{ $car->dealer->business_name }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        @auth
                                                            <form action="{{ route('favorites.store', $car->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Add to Favorites">
                                                                    <i class="fas fa-heart"></i>
                                                                </button>
                                                            </form>
                                                        @endauth
                                                        <a href="{{ route('cars.show', $car->slug) }}" class="btn btn-outline-primary btn-sm">
                                                            View Details <i class="fas fa-arrow-right ms-1"></i>
                                                        </a>
                                                        @auth
                                                            <a href="{{ route('bookings.create', ['vehicle' => 'car', 'id' => $car->id]) }}" class="btn btn-success btn-sm">
                                                                <i class="fas fa-calendar-check me-1"></i> Book Now
                                                            </a>
                                                        @else
                                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#loginRequiredModal{{ $car->id }}">
                                                                <i class="fas fa-calendar-check me-1"></i> Book Now
                                                            </button>
                                                        @endauth
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $cars->links() }}
                    </div>
                @else
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-car fa-3x text-muted mb-3"></i>
                            <h5>No cars found</h5>
                            <p class="text-muted">Try adjusting your filters or search criteria</p>
                            <a href="{{ route('cars.index') }}" class="btn btn-primary">View All Cars</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Load models when make is selected
    document.getElementById('makeSelect').addEventListener('change', function() {
        const makeId = this.value;
        const modelSelect = document.getElementById('modelSelect');
        
        // Clear existing models
        modelSelect.innerHTML = '<option value="">All Models</option>';
        
        if (makeId) {
            fetch(`{{ url('cars/ajax/models') }}?make_id=${makeId}`)
                .then(response => response.json())
                .then(models => {
                    models.forEach(model => {
                        const option = document.createElement('option');
                        option.value = model.id;
                        option.textContent = model.name;
                        modelSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading models:', error));
        }
    });

    // Auto-submit filter form on radio change
    document.querySelectorAll('#filterForm input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Optional: Auto-submit on filter change
            // document.getElementById('filterForm').submit();
        });
    });
</script>
@endpush

<!-- Login Required Modals -->
@foreach($cars as $car)
    @guest
    <div class="modal fade" id="loginRequiredModal{{ $car->id }}" tabindex="-1" aria-labelledby="loginRequiredModalLabel{{ $car->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginRequiredModalLabel{{ $car->id }}">Login Required</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please login or register to book this vehicle.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-success">Register</a>
                </div>
            </div>
        </div>
    </div>
    @endguest
@endforeach
@endsection
