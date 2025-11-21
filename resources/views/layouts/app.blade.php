<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $metaTitle ?? config('app.name', 'CarMarketplace') }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Buy and sell new and used cars online' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">
                                CarMarketplace
                            </a>
                        </div>
                        <div class="hidden sm:ml-10 sm:flex sm:space-x-8">
                            <a href="{{ route('cars.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900">
                                Buy Cars
                            </a>
                            <a href="{{ route('cars.index', ['condition' => 'new']) }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-900">
                                New Cars
                            </a>
                            <a href="{{ route('cars.index', ['condition' => 'used']) }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-900">
                                Used Cars
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center">
                        @auth
                            @if(auth()->user()->isDealer())
                                <a href="{{ route('dealer.dashboard') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                    Dashboard
                                </a>
                            @endif
                            <a href="{{ route('favorites.index') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                Favorites
                            </a>
                            <a href="{{ route('alerts.index') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                Alerts
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="ml-4 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Register
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative" role="alert">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">About Us</h3>
                        <p class="text-gray-400 text-sm">
                            India's leading car marketplace for buying and selling new and used cars.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('cars.index') }}" class="text-gray-400 hover:text-white">Browse Cars</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Sell Your Car</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Dealer Registration</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Support</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-400 hover:text-white">Help Center</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Contact Us</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Legal</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400 text-sm">
                    © {{ date('Y') }} CarMarketplace. All rights reserved.
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
