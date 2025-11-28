<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <title>{{ $metaTitle ?? config('app.name', 'CarMessy') }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Buy and sell new and used cars online' }}">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    
 
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-car"></i> CarMarketplace
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cars.index') }}">Buy Cars</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cars.index', ['condition' => 'new']) }}">New Cars</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cars.index', ['condition' => 'used']) }}">Used Cars</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cars.index') }}">Sell Car</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    @auth
                        @if(auth()->user()->isDealer())
                            <a href="{{ route('dealer.dashboard') }}" class="btn btn-outline-primary me-2">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        @endif
                        <a href="{{ route('favorites.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-heart"></i> Favorites
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main>
        @yield('banner')
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-white mt-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-car"></i> CarMarketplace
                    </h5>
                    <p class="text-muted">
                        India's leading car marketplace for buying and selling new and used cars.
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('cars.index') }}" class="text-muted text-decoration-none">Browse Cars</a></li>
                        <li class="mb-2"><a href="{{ route('cars.index', ['condition' => 'new']) }}" class="text-muted text-decoration-none">New Cars</a></li>
                        <li class="mb-2"><a href="{{ route('cars.index', ['condition' => 'used']) }}" class="text-muted text-decoration-none">Used Cars</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Sell Your Car</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Dealer Registration</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Support</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Contact Us</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">FAQ</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Car Insurance</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Car Loan</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Legal</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Terms of Service</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Disclaimer</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="text-muted mb-0">&copy; {{ date('Y') }} CarMarketplace. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
