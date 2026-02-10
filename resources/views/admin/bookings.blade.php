<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 18px;">Manage Bookings</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.bookings') }}" style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 4px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px;">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Name, Email, Phone..." 
                       class="form-control" style="width: 100%; padding: 6px 10px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px;">Status</label>
                <select name="status" class="form-control" style="width: 100%; padding: 6px 10px;">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px;">Vehicle Type</label>
                <select name="vehicle_type" class="form-control" style="width: 100%; padding: 6px 10px;">
                    <option value="">All Types</option>
                    <option value="car" {{ request('vehicle_type') == 'car' ? 'selected' : '' }}>Car</option>
                    <option value="bike" {{ request('vehicle_type') == 'bike' ? 'selected' : '' }}>Bike</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px;">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="form-control" style="width: 100%; padding: 6px 10px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px;">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="form-control" style="width: 100%; padding: 6px 10px;">
            </div>
        </div>
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter me-1"></i> Apply Filters
            </button>
            <a href="{{ route('admin.bookings') }}" class="btn btn-secondary">
                <i class="fas fa-redo me-1"></i> Reset
            </a>
        </div>
    </form>

    @if($bookings->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Customer</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Vehicle</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Booking Date</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Time Slot</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">City</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Status</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Booked On</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr data-booking-id="{{ $booking->id }}" style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;">#{{ $booking->id }}</td>
                        <td style="padding: 10px;">
                            <strong>{{ $booking->full_name }}</strong><br>
                            <small style="color: #666;">{{ $booking->email }}</small><br>
                            <small style="color: #666;">{{ $booking->phone_number }}</small>
                        </td>
                        <td style="padding: 10px;">
                            @if($booking->vehicle_type === 'car' && $booking->vehicle)
                                <strong>{{ $booking->vehicle->make->name ?? 'N/A' }} {{ $booking->vehicle->model->name ?? 'N/A' }}</strong>
                                <br>
                                <small style="color: #666;">{{ $booking->vehicle->year ?? '' }} • ₹{{ number_format($booking->vehicle->price ?? 0, 0) }}</small>
                            @else
                                <span style="color: #666;">Vehicle #{{ $booking->vehicle_id }}</span>
                            @endif
                        </td>
                        <td style="padding: 10px;">{{ $booking->preferred_booking_date->format('d M Y') }}</td>
                        <td style="padding: 10px;">{{ $booking->preferred_time_slot }}</td>
                        <td style="padding: 10px;">{{ $booking->city }}</td>
                        <td style="padding: 10px;">
                            <span class="booking-status-badge status-{{ $booking->status }}" id="status-badge-{{ $booking->id }}">
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
                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                <select class="form-control booking-status-select" 
                                        data-booking-id="{{ $booking->id }}" 
                                        style="padding: 4px 8px; font-size: 12px; min-width: 120px;">
                                    <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @if($booking->vehicle_type === 'car' && $booking->vehicle)
                                    <a href="{{ route('cars.show', $booking->vehicle->slug) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       target="_blank"
                                       style="padding: 4px 8px; font-size: 12px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                            </div>
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
            <h3 style="color: #23282d; margin-bottom: 15px;">No Bookings Found</h3>
            <p style="color: #666; font-size: 16px;">
                @if(request()->anyFilled(['search', 'status', 'vehicle_type', 'date_from', 'date_to']))
                    No bookings match your filter criteria. Try adjusting your filters.
                @else
                    There are no bookings in the system yet.
                @endif
            </p>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status change
    document.querySelectorAll('.booking-status-select').forEach(select => {
        select.addEventListener('change', function() {
            const bookingId = this.dataset.bookingId;
            const newStatus = this.value;
            const oldValue = this.options[this.selectedIndex].defaultSelected ? this.value : null;
            
            // Show loading state
            const originalValue = this.value;
            this.disabled = true;
            
            // Make API call
            fetch(`/admin/bookings/${bookingId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge
                    const badge = document.getElementById(`status-badge-${bookingId}`);
                    if (badge) {
                        badge.innerHTML = getStatusBadge(newStatus);
                    }
                    
                    // Show success message
                    showNotification('Booking status updated successfully!', 'success');
                } else {
                    // Revert select
                    this.value = originalValue;
                    showNotification('Failed to update booking status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert select
                this.value = originalValue;
                showNotification('An error occurred while updating status', 'error');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
    
    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge bg-warning text-dark">Pending</span>',
            'confirmed': '<span class="badge bg-success">Confirmed</span>',
            'cancelled': '<span class="badge bg-danger">Cancelled</span>',
            'completed': '<span class="badge bg-info">Completed</span>'
        };
        return badges[status] || badges['pending'];
    }
    
    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Add CSS animations
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
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
});
</script>
@endpush

