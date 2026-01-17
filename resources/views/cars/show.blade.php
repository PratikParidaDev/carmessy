@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm">
            <ol class="flex items-center space-x-2 text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-blue-600">Home</a></li>
                <li>/</li>
                <li><a href="{{ route('cars.index') }}" class="hover:text-blue-600">Cars</a></li>
                <li>/</li>
                <li class="text-gray-900">{{ $car->make->name }} {{ $car->model->name }}</li>
            </ol>
        </nav>
       

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Hero Section: Image Gallery & Key Info -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Main Image Gallery -->
                    <div class="relative">
                        @php
                            $images = $car->getMedia('images');
                            $firstImage = $images->first();
                            if ($firstImage) {
                                $mainImage = asset('storage/' . $firstImage->id . '/' . $firstImage->file_name);
                            } else {
                                $mainImage = 'https://via.placeholder.com/800x500?text=No+Image';
                            }
                        @endphp
                        <img id="mainImage" src="{{ $mainImage }}" alt="{{ $car->title }}" class="w-full h-[500px] object-cover" onerror="this.onerror=null; this.src='https://via.placeholder.com/800x500?text=No+Image';">
                        
                        <!-- Favorite Button -->
                        <button onclick="toggleFavorite({{ $car->id }})" class="absolute top-4 right-4 bg-white rounded-full p-3 shadow-lg hover:bg-red-50 transition" title="Add to Favorites">
                            <span id="favorite-icon" class="text-2xl">{{ $car->isFavorited() ? '❤️' : '🤍' }}</span>
                        </button>

                        <!-- Image Counter -->
                        @if($images->count() > 1)
                        <div class="absolute bottom-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                            <span id="currentImageIndex">1</span> / {{ $images->count() }}
                        </div>
                        @endif
                    </div>

                    <!-- Thumbnail Gallery -->
                    @if($images->count() > 1)
                    <div class="p-4 bg-gray-50 border-t">
                        <div class="grid grid-cols-6 gap-2">
                            @foreach($images as $index => $image)
                                @php
                                    $thumbUrl = asset('storage/' . $image->id . '/' . $image->file_name);
                                @endphp
                                <img src="{{ $thumbUrl }}" 
                                     alt="Car image {{ $index + 1 }}" 
                                     class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-75 transition border-2 border-transparent hover:border-blue-500 thumbnail-image" 
                                     data-index="{{ $index }}"
                                     onclick="changeMainImage({{ $index }}, '{{ $thumbUrl }}')"
                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/150x100?text=Image';">
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Car Title & Price Section -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-6">
                        <div class="flex-1">
                            <h1 class="text-4xl font-bold text-gray-900 mb-3">{{ $car->title }}</h1>
                            <div class="flex flex-wrap items-center gap-4 text-gray-600 mb-4">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $car->city->name }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ number_format($car->views) }} views
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $car->year }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="border-t border-b py-6 mb-6">
                        <div class="flex items-baseline">
                            <span class="text-5xl font-bold text-blue-600">{{ $car->getFormattedPrice() }}</span>
                            @if($car->condition === 'new')
                                <span class="ml-3 px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">New</span>
                            @elseif($car->condition === 'certified')
                                <span class="ml-3 px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">Certified</span>
                            @endif
                        </div>
                    </div>

                    <!-- Key Specifications Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg">
                            <div class="text-gray-600 text-sm mb-1">Mileage</div>
                            <div class="text-xl font-bold text-gray-900">{{ number_format($car->mileage) }} km</div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg">
                            <div class="text-gray-600 text-sm mb-1">Fuel Type</div>
                            <div class="text-xl font-bold text-gray-900">{{ ucfirst($car->fuel_type) }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg">
                            <div class="text-gray-600 text-sm mb-1">Transmission</div>
                            <div class="text-xl font-bold text-gray-900">{{ ucfirst($car->transmission) }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg">
                            <div class="text-gray-600 text-sm mb-1">Owners</div>
                            <div class="text-xl font-bold text-gray-900">{{ $car->owners }}</div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Specifications -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Specifications
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Make
                            </span>
                            <span class="font-semibold text-gray-900">{{ $car->make->name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Model
                            </span>
                            <span class="font-semibold text-gray-900">{{ $car->model->name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Engine
                            </span>
                            <span class="font-semibold text-gray-900">{{ $car->engine_capacity ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Power
                            </span>
                            <span class="font-semibold text-gray-900">{{ $car->power ?? 'N/A' }} HP</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Mileage (KMPL)
                            </span>
                            <span class="font-semibold text-gray-900">{{ $car->mileage_kmpl ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                                Color
                            </span>
                            <span class="font-semibold text-gray-900">{{ $car->exterior_color }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Seats
                            </span>
                            <span class="font-semibold text-gray-900">{{ $car->seats }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Doors
                            </span>
                            <span class="font-semibold text-gray-900">{{ $car->doors }}</span>
                        </div>
                    </div>
                </div>

                <!-- Features Section -->
                @if($car->features && count($car->features) > 0)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Features
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $allFeatures = \App\Models\Feature::getActive();
                            $featureMap = $allFeatures->pluck('icon', 'name')->toArray();
                        @endphp
                        @foreach($car->features as $featureName)
                            @php
                                $feature = $allFeatures->firstWhere('name', $featureName);
                                $icon = $feature ? $feature->icon : 'fas fa-check-circle';
                            @endphp
                            <div class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                <i class="{{ $icon }} text-green-600 mr-3 text-lg"></i>
                                <span class="text-gray-800 font-medium">{{ $featureName }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Safety Features Section -->
                @if($car->safety_features && count($car->safety_features) > 0)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Safety Features
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $allSafetyFeatures = \App\Models\SafetyFeature::getActive();
                        @endphp
                        @foreach($car->safety_features as $safetyFeatureName)
                            @php
                                $safetyFeature = $allSafetyFeatures->firstWhere('name', $safetyFeatureName);
                                $icon = $safetyFeature ? $safetyFeature->icon : 'fas fa-shield-alt';
                            @endphp
                            <div class="flex items-center p-3 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                <i class="{{ $icon }} text-red-600 mr-3 text-lg"></i>
                                <span class="text-gray-800 font-medium">{{ $safetyFeatureName }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Description -->
                @if($car->description)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Description</h2>
                    <div class="prose max-w-none ck-content" style="color: #374151;">
                        <div class="text-gray-700 leading-relaxed" style="line-height: 1.75;">{!! $car->description !!}</div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Contact & Dealer Info -->
            <div class="lg:col-span-1">
                <!-- Dealer Information Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6 sticky top-4">
                    <div class="text-center mb-6 pb-6 border-b">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $car->dealer->business_name }}</h3>
                        @if($car->dealer->is_verified)
                            <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Verified Dealer
                            </span>
                        @endif
                        <div class="mt-4 space-y-2 text-sm text-gray-600">
                            <p class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $car->dealer->city->name }}
                            </p>
                            @if($car->dealer->rating > 0)
                                <p class="flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    {{ number_format($car->dealer->rating, 1) }} ({{ $car->dealer->total_reviews }} reviews)
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <form action="{{ route('inquiries.store', $car) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ auth()->user()->name ?? '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Email</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ auth()->user()->email ?? '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Phone</label>
                            <input type="tel" name="phone" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message (optional)</label>
                            <textarea name="message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg font-semibold transition shadow-lg">
                            Send Inquiry
                        </button>
                    </form>

                    <!-- Quick Contact Buttons -->
                    <div class="mt-6 space-y-3 pt-6 border-t">
                        <a href="tel:{{ $car->dealer->phone }}" class="flex items-center justify-center w-full bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-semibold transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Call Dealer
                        </a>
                        @if($car->dealer->whatsapp)
                        <a href="https://wa.me/{{ str_replace(['+', ' '], '', $car->dealer->whatsapp) }}" target="_blank" class="flex items-center justify-center w-full bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                            WhatsApp
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Cars Section -->
        @if($similarCars->count() > 0)
        <div class="mt-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-3xl font-bold text-gray-900">Similar Cars</h2>
                <a href="{{ route('cars.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold">View All →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($similarCars as $similarCar)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1">
                        <a href="{{ route('cars.show', $similarCar->slug) }}">
                            @php
                                $similarImage = $similarCar->getFirstMedia('images');
                                if ($similarImage) {
                                    $similarImageUrl = asset('storage/' . $similarImage->id . '/' . $similarImage->file_name);
                                } else {
                                    $similarImageUrl = 'https://via.placeholder.com/400x300?text=No+Image';
                                }
                            @endphp
                            <img src="{{ $similarImageUrl }}" alt="{{ $similarCar->title }}" class="w-full h-48 object-cover" onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=No+Image';">
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-2 text-gray-900 line-clamp-2">{{ $similarCar->title }}</h3>
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-2xl font-bold text-blue-600">{{ $similarCar->getFormattedPrice() }}</p>
                                    <span class="text-sm text-gray-500">{{ $similarCar->year }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 mb-3">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $similarCar->city->name }}
                                </div>
                                <div class="text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-semibold">
                                    View Details
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.ck-content {
    font-size: 16px;
    line-height: 1.75;
}

.ck-content h1,
.ck-content h2,
.ck-content h3 {
    font-weight: bold;
    margin-top: 1.5em;
    margin-bottom: 0.75em;
}

.ck-content h1 {
    font-size: 2em;
}

.ck-content h2 {
    font-size: 1.5em;
}

.ck-content h3 {
    font-size: 1.25em;
}

.ck-content p {
    margin-bottom: 1em;
}

.ck-content ul,
.ck-content ol {
    margin: 1em 0;
    padding-left: 2em;
}

.ck-content li {
    margin-bottom: 0.5em;
}

.ck-content blockquote {
    border-left: 4px solid #e5e7eb;
    padding-left: 1em;
    margin: 1.5em 0;
    font-style: italic;
    color: #6b7280;
}

.ck-content a {
    color: #2563eb;
    text-decoration: underline;
}

.ck-content a:hover {
    color: #1d4ed8;
}

.ck-content strong {
    font-weight: 600;
}

.ck-content em {
    font-style: italic;
}
</style>
@endpush

@push('scripts')
<script>
function toggleFavorite(carId) {
    @guest
        alert('Please login to add favorites');
        window.location.href = '{{ route("login") }}';
        return;
    @endguest

    fetch(`/favorites/${carId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('favorite-icon').textContent = '❤️';
        } else {
            // Remove favorite
            fetch(`/favorites/${carId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('favorite-icon').textContent = '🤍';
                }
            });
        }
    });
}

// Image gallery functionality
@php
    $imageUrls = $car->getMedia('images')->map(function($img) {
        return asset('storage/' . $img->id . '/' . $img->file_name);
    })->values()->all();
@endphp
const images = @json($imageUrls);

let currentImageIndex = 0;

function changeMainImage(index, imageUrl) {
    currentImageIndex = index;
    document.getElementById('mainImage').src = imageUrl;
    document.getElementById('currentImageIndex').textContent = index + 1;
    
    // Update thumbnail borders
    document.querySelectorAll('.thumbnail-image').forEach((thumb, i) => {
        if (i === index) {
            thumb.classList.add('border-blue-500');
            thumb.classList.remove('border-transparent');
        } else {
            thumb.classList.remove('border-blue-500');
            thumb.classList.add('border-transparent');
        }
    });
}

// Initialize first thumbnail as active
document.addEventListener('DOMContentLoaded', function() {
    if (images.length > 1) {
        const firstThumb = document.querySelector('.thumbnail-image[data-index="0"]');
        if (firstThumb) {
            firstThumb.classList.add('border-blue-500');
            firstThumb.classList.remove('border-transparent');
        }
    }
});

// Keyboard navigation for images
document.addEventListener('keydown', function(e) {
    if (images.length <= 1) return;
    
    if (e.key === 'ArrowLeft' && currentImageIndex > 0) {
        currentImageIndex--;
        changeMainImage(currentImageIndex, images[currentImageIndex]);
    } else if (e.key === 'ArrowRight' && currentImageIndex < images.length - 1) {
        currentImageIndex++;
        changeMainImage(currentImageIndex, images[currentImageIndex]);
    }
});
</script>
@endpush
@endsection
