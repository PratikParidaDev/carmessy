@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Images and Details -->
        <div class="lg:col-span-2">
            <!-- Image Gallery -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                @php
                    $images = $car->getMedia('images');
                    $mainImage = $images->first()?->getUrl() ?? 'https://via.placeholder.com/800x600?text=No+Image';
                @endphp
                <img id="mainImage" src="{{ $mainImage }}" alt="{{ $car->title }}" class="w-full h-96 object-cover">
                
                @if($images->count() > 1)
                <div class="p-4 grid grid-cols-6 gap-2">
                    @foreach($images as $image)
                        <img src="{{ $image->getUrl() }}" alt="Car image" class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 transition" onclick="document.getElementById('mainImage').src = this.src">
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Car Title and Price -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">{{ $car->title }}</h1>
                        <div class="flex items-center text-gray-600 space-x-4">
                            <span>📍 {{ $car->city->name }}</span>
                            <span>👁️ {{ number_format($car->views) }} views</span>
                        </div>
                    </div>
                    <button onclick="toggleFavorite({{ $car->id }})" class="text-red-500 hover:text-red-600 text-2xl">
                        <span id="favorite-icon">{{ $car->isFavorited() ? '❤️' : '🤍' }}</span>
                    </button>
                </div>
                <div class="text-4xl font-bold text-blue-600 mb-4">
                    {{ $car->getFormattedPrice() }}
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="text-gray-600">Year</div>
                        <div class="font-semibold">{{ $car->year }}</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="text-gray-600">Mileage</div>
                        <div class="font-semibold">{{ number_format($car->mileage) }} km</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="text-gray-600">Fuel</div>
                        <div class="font-semibold">{{ ucfirst($car->fuel_type) }}</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="text-gray-600">Transmission</div>
                        <div class="font-semibold">{{ ucfirst($car->transmission) }}</div>
                    </div>
                </div>
            </div>

            <!-- Specifications -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">Specifications</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Make</span>
                        <span class="font-semibold">{{ $car->make->name }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Model</span>
                        <span class="font-semibold">{{ $car->model->name }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Engine</span>
                        <span class="font-semibold">{{ $car->engine_capacity ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Power</span>
                        <span class="font-semibold">{{ $car->power ?? 'N/A' }} HP</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Mileage (KMPL)</span>
                        <span class="font-semibold">{{ $car->mileage_kmpl ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Color</span>
                        <span class="font-semibold">{{ $car->exterior_color }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Seats</span>
                        <span class="font-semibold">{{ $car->seats }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Owners</span>
                        <span class="font-semibold">{{ $car->owners }}</span>
                    </div>
                </div>
            </div>

            <!-- Features -->
            @if($car->features)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">Features</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($car->features as $feature)
                        <div class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            <span>{{ $feature }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Description -->
            @if($car->description)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">Description</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $car->description }}</p>
            </div>
            @endif
        </div>

        <!-- Right Column - Contact Form -->
        <div class="lg:col-span-1">
            <!-- Dealer Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 sticky top-4">
                <h3 class="text-xl font-bold mb-4">Dealer Information</h3>
                <div class="mb-4">
                    <h4 class="font-semibold text-lg">{{ $car->dealer->business_name }}</h4>
                    @if($car->dealer->is_verified)
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">✓ Verified</span>
                    @endif
                    <div class="mt-2 text-sm text-gray-600">
                        <p>📍 {{ $car->dealer->city->name }}</p>
                        @if($car->dealer->rating > 0)
                            <p>⭐ {{ $car->dealer->rating }} ({{ $car->dealer->total_reviews }} reviews)</p>
                        @endif
                    </div>
                </div>

                <!-- Contact Form -->
                <form action="{{ route('inquiries.store', $car) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <input type="text" name="name" placeholder="Your Name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ auth()->user()->name ?? '' }}">
                    </div>
                    <div>
                        <input type="email" name="email" placeholder="Your Email" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ auth()->user()->email ?? '' }}">
                    </div>
                    <div>
                        <input type="tel" name="phone" placeholder="Your Phone" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <textarea name="message" rows="4" placeholder="Message (optional)" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        Send Inquiry
                    </button>
                </form>

                <div class="mt-4 space-y-2">
                    <a href="tel:{{ $car->dealer->phone }}" class="block text-center border-2 border-gray-300 hover:border-blue-600 px-6 py-2 rounded-lg font-semibold transition">
                        📞 Call Dealer
                    </a>
                    @if($car->dealer->whatsapp)
                    <a href="https://wa.me/{{ str_replace(['+', ' '], '', $car->dealer->whatsapp) }}" target="_blank" class="block text-center border-2 border-green-500 text-green-600 hover:bg-green-500 hover:text-white px-6 py-2 rounded-lg font-semibold transition">
                        💬 WhatsApp
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Cars -->
    @if($similarCars->count() > 0)
    <div class="mt-12">
        <h2 class="text-3xl font-bold mb-6">Similar Cars</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($similarCars as $similarCar)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img src="{{ $similarCar->getFirstMediaUrl('images') ?: 'https://via.placeholder.com/400x300' }}" alt="{{ $similarCar->title }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2 truncate">{{ $similarCar->title }}</h3>
                        <p class="text-2xl font-bold text-blue-600 mb-2">{{ $similarCar->getFormattedPrice() }}</p>
                        <a href="{{ route('cars.show', $similarCar->slug) }}" class="block text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

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
</script>
@endpush
@endsection
