<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 18px;">My Bookings 
            <span id="live-update-indicator" style="display: none; margin-left: 10px; font-size: 12px; color: #28a745;">
                <i class="fas fa-circle" style="animation: pulse 2s infinite;"></i> Live Updates
            </span>
        </h2>
        <a href="{{ route('cars.index') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Book Another Vehicle
        </a>
    </div>

    @if($bookings->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Vehicle</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Booking Date</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Time Slot</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">City</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Pickup Type</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Payment</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Status</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Booked On</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr data-booking-id="{{ $booking->id }}" style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;">
                            @if($booking->vehicle_type === 'car' && $booking->vehicle)
                                <strong>{{ $booking->vehicle->make->name }} {{ $booking->vehicle->model->name }}</strong>
                                <br>
                                <small style="color: #666;">{{ $booking->vehicle->year }} • ₹{{ number_format($booking->vehicle->price, 0) }}</small>
                            @else
                                Vehicle #{{ $booking->vehicle_id }}
                            @endif
                        </td>
                        <td style="padding: 10px;">{{ $booking->preferred_booking_date->format('d M Y') }}</td>
                        <td style="padding: 10px;">{{ $booking->preferred_time_slot }}</td>
                        <td style="padding: 10px;">{{ $booking->city }}</td>
                        <td style="padding: 10px;">{{ ucfirst(str_replace('_', ' ', $booking->pickup_type)) }}</td>
                        <td style="padding: 10px;">{{ ucfirst($booking->payment_mode) }}</td>
                        <td style="padding: 10px;">
                            <span id="status-badge-{{ $booking->id }}">
                                @if($booking->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge bg-success">Confirmed</span>
                                @elseif($booking->status === 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @elseif($booking->status === 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @endif
                            </span>
                        </td>
                        <td style="padding: 10px;">{{ $booking->created_at->format('d M Y, h:i A') }}</td>
                        <td style="padding: 10px;">
                            @if($booking->vehicle_type === 'car' && $booking->vehicle)
                                <a href="{{ route('cars.show', $booking->vehicle->slug) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   style="padding: 4px 8px; font-size: 12px;">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            @endif
                        </td>
                    </tr>
                    @if($booking->message)
                    <tr>
                        <td colspan="9" style="padding: 10px; background: #f9f9f9; font-size: 13px; color: #666;">
                            <strong>Special Request:</strong> {{ $booking->message }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div style="color: #666; font-size: 13px;">
                Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings
            </div>
            <div>
                {{ $bookings->links() }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 8px; margin-top: 20px;">
            <i class="fas fa-calendar-times" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #23282d; margin-bottom: 15px;">No Bookings Yet</h3>
            <p style="color: #666; font-size: 16px; margin-bottom: 25px;">
                You haven't made any bookings yet. Start booking your favorite vehicle!
            </p>
            <a href="{{ route('cars.index') }}" class="btn btn-primary" style="padding: 12px 24px;">
                <i class="fas fa-search me-1"></i> Browse Vehicles
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
(function() {
    let lastCheck = new Date().toISOString();
    let updateInterval;
    
    function startLiveUpdates() {
        console.log('Starting live updates for bookings in dashboard...');
        
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
            console.log('Dashboard update check response:', data);
            
            if (data.success && data.bookings && data.bookings.length > 0) {
                console.log(`Found ${data.bookings.length} updated booking(s)`);
                
                // Update last check timestamp
                lastCheck = data.timestamp;
                
                // Update booking statuses in the table
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
        
        // Find the row for this booking
        const row = document.querySelector(`tr[data-booking-id="${booking.id}"]`);
        if (!row) {
            console.warn('Booking row not found for ID:', booking.id);
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
        row.style.transition = 'background-color 0.3s';
        row.style.backgroundColor = '#fff3cd';
        setTimeout(() => {
            row.style.transition = 'background-color 2s';
            row.style.backgroundColor = '';
        }, 2000);
    }
    
    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge bg-warning text-dark">Pending</span>',
            'confirmed': '<span class="badge bg-success">Confirmed</span>',
            'cancelled': '<span class="badge bg-danger">Cancelled</span>',
            'completed': '<span class="badge bg-info">Completed</span>'
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

