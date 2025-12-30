<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>All Cars</h2>
        <a href="{{ route('dashboard.cars.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New
        </a>
    </div>

    @if(!isset($listingFields))
        @php
            // Fallback: If listingFields is not set, use default fields
            $listingFields = [
                'title' => ['label' => 'Title'],
                'make_id' => ['label' => 'Make'],
                'model_id' => ['label' => 'Model'],
                'year' => ['label' => 'Year'],
                'price' => ['label' => 'Price'],
                'condition' => ['label' => 'Condition'],
                'status' => ['label' => 'Status'],
                'created_at' => ['label' => 'Created Date'],
            ];
        @endphp
    @endif

    @if($cars->count() > 0)
        <!-- WordPress-style table wrapper -->
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="width: 40px; padding: 8px;">
                            <input type="checkbox" id="select-all" style="cursor: pointer;">
                        </th>
                        <th style="width: 80px;">Image</th>
                        @foreach($listingFields as $fieldName => $field)
                            @if($fieldName === 'title')
                                <th style="min-width: 200px;">{{ $field['label'] }}</th>
                            @elseif($fieldName === 'make_id' || $fieldName === 'model_id')
                                <th>{{ $field['label'] }}</th>
                            @elseif($fieldName === 'status')
                                <th style="width: 100px;">{{ $field['label'] }}</th>
                            @elseif($fieldName === 'created_at')
                                <th style="width: 120px;">{{ $field['label'] }}</th>
                            @else
                                <th>{{ $field['label'] }}</th>
                            @endif
                        @endforeach
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cars as $car)
                    <tr data-car-id="{{ $car->id }}">
                        <td>
                            <input type="checkbox" name="car_ids[]" value="{{ $car->id }}" class="car-checkbox" style="cursor: pointer;">
                        </td>
                        <td>
                            @php
                                $firstImage = $car->getFirstMedia('images');
                                $imageUrl = $firstImage ? $firstImage->getUrl() : null;
                            @endphp
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" 
                                     alt="{{ $car->title }}" 
                                     style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;"
                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'40\'%3E%3Crect width=\'60\' height=\'40\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'12\'%3ENo Image%3C/text%3E%3C/svg%3E'; this.style.display='block';">
                            @else
                                <div style="width: 60px; height: 40px; background: #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center; border: 1px solid #ccc;">
                                    <i class="fas fa-car" style="color: #999; font-size: 20px;"></i>
                                </div>
                            @endif
                        </td>
                        @foreach($listingFields as $fieldName => $field)
                            <td>
                                @if($fieldName === 'title')
                                    <strong>
                                        <a href="{{ route('dashboard.cars.edit', $car->id) }}" style="color: #2271b1; text-decoration: none;">
                                            {{ $car->title }}
                                        </a>
                                    </strong>
                                @elseif($fieldName === 'make_id')
                                    {{ $car->make->name ?? 'N/A' }}
                                @elseif($fieldName === 'model_id')
                                    {{ $car->model->name ?? 'N/A' }}
                                @elseif($fieldName === 'year')
                                    {{ $car->year }}
                                @elseif($fieldName === 'price')
                                    ₹ {{ number_format($car->price, 0) }}
                                @elseif($fieldName === 'condition')
                                    <span style="text-transform: capitalize;">{{ $car->condition }}</span>
                                @elseif($fieldName === 'status')
                                    <span class="status-badge status-{{ $car->status }}">
                                        {{ ucfirst($car->status) }}
                                    </span>
                                @elseif($fieldName === 'created_at')
                                    {{ $car->created_at->format('M j, Y') }}
                                @else
                                    {{ $car->{$fieldName} ?? 'N/A' }}
                                @endif
                            </td>
                        @endforeach
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('dashboard.cars.edit', $car->id) }}" 
                                   class="btn btn-secondary" 
                                   style="padding: 4px 8px; font-size: 12px;">
                                    Edit
                                </a>
                                <form action="{{ route('dashboard.cars.delete', $car->id) }}" 
                                      method="POST" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this car?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-danger" 
                                            style="padding: 4px 8px; font-size: 12px;">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Bulk Actions (WordPress-style, ready for future implementation) -->
        <div style="margin-top: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; display: flex; align-items: center; gap: 10px;">
            <select name="bulk_action" id="bulk-action-selector" style="padding: 5px;">
                <option value="">Bulk Actions</option>
                <!-- Future: Add bulk delete, bulk status change, etc. -->
            </select>
            <button type="button" class="btn btn-secondary" id="do-bulk-action" disabled style="padding: 5px 10px;">
                Apply
            </button>
            <span style="color: #666; font-size: 13px;">
                {{ $cars->total() }} {{ Str::plural('item', $cars->total()) }}
            </span>
        </div>

        <!-- Pagination -->
        <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div style="color: #666; font-size: 13px;">
                Showing {{ $cars->firstItem() }} to {{ $cars->lastItem() }} of {{ $cars->total() }} cars
            </div>
            <div>
                {{ $cars->links() }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px;">
            <i class="fas fa-car" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <p style="color: #666; font-size: 16px; margin-bottom: 10px;">No cars found.</p>
            <p style="color: #999; font-size: 14px; margin-bottom: 20px;">Get started by creating your first car listing.</p>
            <a href="{{ route('dashboard.cars.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Your First Car
            </a>
        </div>
    @endif
</div>

<script>
    // Select all checkbox functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const carCheckboxes = document.querySelectorAll('.car-checkbox');
        const bulkActionBtn = document.getElementById('do-bulk-action');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                carCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActionButton();
            });
        }

        carCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBulkActionButton();
                // Update select all checkbox state
                if (selectAll) {
                    const allChecked = Array.from(carCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(carCheckboxes).some(cb => cb.checked);
                    selectAll.checked = allChecked;
                    selectAll.indeterminate = someChecked && !allChecked;
                }
            });
        });

        function updateBulkActionButton() {
            const checked = Array.from(carCheckboxes).some(cb => cb.checked);
            if (bulkActionBtn) {
                bulkActionBtn.disabled = !checked;
            }
        }

        // Real-time status updates using polling
        let lastCheckTime = new Date().toISOString();
        let updateInterval = null;

        function startRealTimeUpdates() {
            // Poll every 3 seconds for status updates
            updateInterval = setInterval(checkStatusUpdates, 3000);
        }

        function checkStatusUpdates() {
            fetch('{{ route("api.cars.status-updates") }}?last_check=' + encodeURIComponent(lastCheckTime), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.cars && data.cars.length > 0) {
                    data.cars.forEach(car => {
                        updateCarStatus(car);
                    });
                    lastCheckTime = data.timestamp;
                }
            })
            .catch(error => {
                console.error('Error checking status updates:', error);
            });
        }

        function updateCarStatus(carData) {
            // Find the row for this car
            const carRow = document.querySelector(`tr[data-car-id="${carData.id}"]`);
            if (!carRow) return;

            // Update status badge
            const statusCell = carRow.querySelector('.status-badge');
            if (statusCell) {
                statusCell.className = `status-badge status-${carData.status}`;
                statusCell.textContent = carData.status.charAt(0).toUpperCase() + carData.status.slice(1);
                
                // Add animation to show update
                statusCell.style.transition = 'all 0.3s ease';
                statusCell.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    statusCell.style.transform = 'scale(1)';
                }, 300);
            }

            // Show a notification
            if (carData.status === 'approved') {
                showNotification(`🎉 Great news! Your car "${carData.title}" has been approved and is now live!`, 'success');
            } else if (carData.status === 'rejected') {
                showNotification(`Your car "${carData.title}" has been rejected. Please check with admin.`, 'error');
            }
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
                color: white;
                border-radius: 4px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                z-index: 10000;
                max-width: 400px;
                animation: slideIn 0.3s ease-out;
            `;
            notification.textContent = message;
            
            // Add animation
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
                `;
                document.head.appendChild(style);
            }
            
            document.body.appendChild(notification);
            
            // Remove after 5 seconds
            setTimeout(() => {
                notification.style.animation = 'slideIn 0.3s ease-out reverse';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Start real-time updates
        startRealTimeUpdates();

        // Stop updates when page is hidden (to save resources)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                if (updateInterval) {
                    clearInterval(updateInterval);
                    updateInterval = null;
                }
            } else {
                if (!updateInterval) {
                    startRealTimeUpdates();
                }
            }
        });
    });
</script>
