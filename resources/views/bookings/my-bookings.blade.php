@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">My Bookings</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="fas fa-calendar-check me-2"></i>My Bookings
                <span id="live-update-indicator" style="display: none; margin-left: 10px; font-size: 12px; color: #28a745;">
                    <i class="fas fa-circle" style="animation: pulse 2s infinite;"></i> Live Updates
                </span>
            </h2>
            <a href="{{ route('cars.index') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Book Another Vehicle
            </a>
        </div>

        @if($bookings->count() > 0)
            <div class="row g-4">
                @foreach($bookings as $booking)
                    <div class="col-12" data-booking-id="{{ $booking->id }}">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-2">
                                                    @if($booking->vehicle_type === 'car' && $booking->vehicle)
                                                        {{ $booking->vehicle->make->name }} {{ $booking->vehicle->model->name }}
                                                    @else
                                                        Vehicle #{{ $booking->vehicle_id }}
                                                    @endif
                                                </h5>
                                                <div class="row g-2 mb-2">
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-calendar me-1"></i> 
                                                            <strong>Booking Date:</strong> {{ $booking->preferred_booking_date->format('d M Y') }}
                                                        </small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-clock me-1"></i> 
                                                            <strong>Time Slot:</strong> {{ $booking->preferred_time_slot }}
                                                        </small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-map-marker-alt me-1"></i> 
                                                            <strong>City:</strong> {{ $booking->city }}
                                                        </small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-truck me-1"></i> 
                                                            <strong>Pickup:</strong> {{ ucfirst(str_replace('_', ' ', $booking->pickup_type)) }}
                                                        </small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-credit-card me-1"></i> 
                                                            <strong>Payment:</strong> {{ ucfirst($booking->payment_mode) }}
                                                        </small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-calendar-alt me-1"></i> 
                                                            <strong>Booked On:</strong> {{ $booking->created_at->format('d M Y, h:i A') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <div class="mb-3">
                                            <span id="status-badge-{{ $booking->id }}">
                                                @if($booking->status === 'pending')
                                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                                        <i class="fas fa-clock me-1"></i> Pending
                                                    </span>
                                                @elseif($booking->status === 'confirmed')
                                                    <span class="badge bg-success fs-6 px-3 py-2">
                                                        <i class="fas fa-check-circle me-1"></i> Confirmed
                                                    </span>
                                                @elseif($booking->status === 'cancelled')
                                                    <span class="badge bg-danger fs-6 px-3 py-2">
                                                        <i class="fas fa-times-circle me-1"></i> Cancelled
                                                    </span>
                                                @elseif($booking->status === 'completed')
                                                    <span class="badge bg-info fs-6 px-3 py-2">
                                                        <i class="fas fa-check-double me-1"></i> Completed
                                                    </span>
                                                @endif
                                            </span>
                                        </div>
                                        @if($booking->vehicle_type === 'car' && $booking->vehicle)
                                            <a href="{{ route('cars.show', $booking->vehicle->slug) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i> View Vehicle
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                @if($booking->message)
                                    <div class="mt-3 pt-3 border-top">
                                        <small class="text-muted">
                                            <strong>Special Request:</strong> {{ $booking->message }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5>No Bookings Yet</h5>
                    <p class="text-muted">You haven't made any bookings yet. Start booking your favorite vehicle!</p>
                    <a href="{{ route('cars.index') }}" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Browse Vehicles
                    </a>
                </div>
        </div>
    @endif
    </div>
</div>

@push('scripts')
<script>
(function() {
    let lastCheck = new Date().toISOString();
    let updateInterval;
    
    function startLiveUpdates() {
        console.log('Starting live updates for bookings...');
        
        // Show live indicator
        const indicator = document.getElementById('live-update-indicator');
        if (indicator) {
            indicator.style.display = 'inline-block';
        }
        
        // Poll for updates every 5 seconds
        updateInterval = setInterval(checkForUpdates, 5000);
        
        // Also check immediately after 1 second
        setTimeout(checkForUpdates, 1000);
    }
    
    function checkForUpdates() {
        const url = `{{ route('api.bookings.status-updates') }}?last_check=${encodeURIComponent(lastCheck)}`;
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Update check response:', data);
            
            if (data.success && data.bookings && data.bookings.length > 0) {
                console.log(`Found ${data.bookings.length} updated booking(s)`);
                
                // Update last check timestamp
                lastCheck = data.timestamp;
                
                // Update booking statuses
                data.bookings.forEach(booking => {
                    updateBookingStatus(booking);
                });
                
                // Show notification
                showUpdateNotification(data.bookings.length);
            }
            
            // Update timestamp for next check
            if (data.timestamp) {
                lastCheck = data.timestamp;
            }
        })
        .catch(error => {
            console.error('Error checking for updates:', error);
        });
    }
    
    function updateBookingStatus(booking) {
        console.log('Updating booking:', booking.id, 'to status:', booking.status);
        
        // Find the booking card
        const bookingCard = document.querySelector(`[data-booking-id="${booking.id}"]`);
        if (!bookingCard) {
            console.warn('Booking card not found for ID:', booking.id);
            return;
        }
        
        // Update status badge
        const statusBadge = document.getElementById(`status-badge-${booking.id}`);
        if (statusBadge) {
            statusBadge.innerHTML = getStatusBadge(booking.status);
            console.log('Status badge updated');
        } else {
            console.warn('Status badge not found for booking ID:', booking.id);
        }
        
        // Add highlight effect
        bookingCard.style.transition = 'background-color 0.3s';
        bookingCard.style.backgroundColor = '#fff3cd';
        setTimeout(() => {
            bookingCard.style.transition = 'background-color 2s';
            bookingCard.style.backgroundColor = '';
        }, 2000);
    }
    
    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="fas fa-clock me-1"></i> Pending</span>',
            'confirmed': '<span class="badge bg-success fs-6 px-3 py-2"><i class="fas fa-check-circle me-1"></i> Confirmed</span>',
            'cancelled': '<span class="badge bg-danger fs-6 px-3 py-2"><i class="fas fa-times-circle me-1"></i> Cancelled</span>',
            'completed': '<span class="badge bg-info fs-6 px-3 py-2"><i class="fas fa-check-double me-1"></i> Completed</span>'
        };
        return badges[status] || badges['pending'];
    }
    
    function showUpdateNotification(count) {
        // Remove existing notification if any
        let existingNotification = document.getElementById('booking-update-notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Create new notification
        const notification = document.createElement('div');
        notification.id = 'booking-update-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: #28a745;
            color: white;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideIn 0.3s ease;
            font-size: 14px;
        `;
        notification.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            ${count} booking${count > 1 ? 's' : ''} updated!
        `;
        document.body.appendChild(notification);
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 3000);
    }
    
    // Start live updates when page loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startLiveUpdates);
    } else {
        startLiveUpdates();
    }
    
    // Clean up on page unload
    window.addEventListener('beforeunload', function() {
        if (updateInterval) {
            clearInterval(updateInterval);
        }
    });
    
    // Add CSS for animations
    if (!document.getElementById('live-update-styles')) {
        const style = document.createElement('style');
        style.id = 'live-update-styles';
        style.textContent = `
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
})();
</script>
@endpush
@endsection

