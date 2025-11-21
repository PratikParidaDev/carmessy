@extends('layouts.app')

@section('content')
<div class="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Find Your Dream Car Today
            </h1>
            <p class="text-xl mb-8">
                Browse thousands of new and used cars from trusted dealers
            </p>

            <!-- Search Form -->
            <form action="{{ route('cars.index') }}" method="GET" class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <select name="make_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 text-gray-900 focus:ring-2 focus:ring-blue-500">
                            <option value="">All Makes</option>
                            @foreach($popularMakes as $make)
                                <option value="{{ $make->id }}">{{ $make->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="city_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 text-gray-900 focus:ring-2 focus:ring-blue-500">
                            <option value="">All Cities</option>
                            @foreach($popularCities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="number" name="max_price" placeholder="Max Price" class="w-full px-4 py-3 rounded-lg border border-gray-300 text-gray-900 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                            Search Cars
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Featured Cars -->
@if($featuredCars->count() > 0)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-3xl font-bold mb-8">Featured Cars</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($featuredCars as $car)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                <div class="relative">
                    @php
                        $image = $car->getFirstMediaUrl('images');
                    @endphp
                    <img src="{{ $image ?: 'https://via.placeholder.com/400x300?text=No+Image' }}" alt="{{ $car->title }}" class="w-full h-48 object-cover">
                    <div class="absolute top-2 right-2 bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-xs font-semibold">
                        FEATURED
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-2 truncate">{{ $car->title }}</h3>
                    <p class="text-2xl font-bold text-blue-600 mb-2">{{ $car->getFormattedPrice() }}</p>
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                        <span>{{ $car->year }}</span>
                        <span>{{ number_format($car->mileage) }} km</span>
                        <span>{{ ucfirst($car->fuel_type) }}</span>
                    </div>
                    <div class="text-sm text-gray-500 mb-3">
                        <span>📍 {{ $car->city->name }}</span>
                    </div>
                    <a href="{{ route('cars.show', $car->slug) }}" class="block text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        View Details
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- New Cars -->
@if($newCars->count() > 0)
<div class="bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold mb-8">Latest New Cars</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($newCars as $car)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img src="{{ $car->getFirstMediaUrl('images') ?: 'https://via.placeholder.com/400x300' }}" alt="{{ $car->title }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">{{ $car->title }}</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ $car->getFormattedPrice() }}</p>
                        <a href="{{ route('cars.show', $car->slug) }}" class="mt-4 block text-center border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg font-semibold transition">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Popular Makes -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-3xl font-bold mb-8">Popular Brands</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
        @foreach($popularMakes as $make)
            <a href="{{ route('cars.index', ['make_id' => $make->id]) }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition text-center">
                <div class="text-4xl mb-2">🚗</div>
                <h3 class="font-semibold">{{ $make->name }}</h3>
            </a>
        @endforeach
    </div>
</div>
@endsection