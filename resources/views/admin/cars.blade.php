<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>All Cars</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
        </a>
    </div>

    @if($cars->count() > 0)
        <!-- Bulk Actions -->
        <div style="margin-bottom: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; display: flex; align-items: center; gap: 10px;">
            <select name="bulk_action" id="bulk-action-selector" style="padding: 5px;">
                <option value="">Bulk Actions</option>
                <option value="approve">Approve Selected</option>
                <option value="reject">Reject Selected</option>
                <option value="delete">Delete Selected</option>
            </select>
            <button type="button" class="btn btn-secondary" id="do-bulk-action" disabled style="padding: 5px 10px;">
                Apply
            </button>
            <span style="color: #666; font-size: 13px;">
                {{ $cars->total() }} {{ Str::plural('item', $cars->total()) }}
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all" style="cursor: pointer;">
                        </th>
                        <th style="width: 80px;">Image</th>
                        <th>Title</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Year</th>
                        <th>Price</th>
                        <th>Dealer</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="width: 200px;">Actions</th>
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
                        <td>
                            <strong>{{ $car->title }}</strong>
                        </td>
                        <td>{{ $car->make->name ?? 'N/A' }}</td>
                        <td>{{ $car->model->name ?? 'N/A' }}</td>
                        <td>{{ $car->year }}</td>
                        <td>₹ {{ number_format($car->price, 0) }}</td>
                        <td>
                            <small>{{ $car->dealer->user->name ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $car->status }}">
                                {{ ucfirst($car->status) }}
                            </span>
                        </td>
                        <td>{{ $car->created_at->format('M j, Y') }}</td>
                        <td>
                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                @if($car->status === 'pending')
                                <form action="{{ route('admin.cars.approve', $car->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="padding: 4px 8px; font-size: 12px;">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.cars.reject', $car->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px;">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('dashboard.cars.edit', $car->id) }}" class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.cars.delete', $car->id) }}" method="POST" style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this car?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 4px 8px; font-size: 12px;">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
            <p style="color: #666; font-size: 16px;">No cars found.</p>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const carCheckboxes = document.querySelectorAll('.car-checkbox');
        const bulkActionSelector = document.getElementById('bulk-action-selector');
        const bulkActionBtn = document.getElementById('do-bulk-action');

        // Select all functionality
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
                bulkActionBtn.disabled = !checked || !bulkActionSelector.value;
            }
        }

        if (bulkActionSelector) {
            bulkActionSelector.addEventListener('change', updateBulkActionButton);
        }

        // Bulk action handler
        if (bulkActionBtn) {
            bulkActionBtn.addEventListener('click', function() {
                const action = bulkActionSelector.value;
                const checkedBoxes = Array.from(carCheckboxes).filter(cb => cb.checked);
                const carIds = checkedBoxes.map(cb => cb.value);

                if (!action || carIds.length === 0) {
                    alert('Please select an action and at least one car.');
                    return;
                }

                if (!confirm(`Are you sure you want to ${action} ${carIds.length} car(s)?`)) {
                    return;
                }

                let url = '';
                if (action === 'approve') {
                    url = '{{ route("admin.cars.bulk-approve") }}';
                } else if (action === 'reject') {
                    url = '{{ route("admin.cars.bulk-reject") }}';
                } else if (action === 'delete') {
                    url = '{{ route("admin.cars.bulk-delete") }}';
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                                 document.querySelector('input[name="_token"]')?.value;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                carIds.forEach(carId => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'car_ids[]';
                    input.value = carId;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            });
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
            }

            // Update actions column - remove approve/reject buttons if status changed
            const actionsCell = carRow.querySelector('td:last-child');
            if (actionsCell && carData.status !== 'pending') {
                // Remove approve/reject buttons if status is no longer pending
                const approveBtn = actionsCell.querySelector('form[action*="approve"]');
                const rejectBtn = actionsCell.querySelector('form[action*="reject"]');
                if (approveBtn) approveBtn.remove();
                if (rejectBtn) rejectBtn.remove();
            }

            // Show a notification
            showNotification(`Car "${carData.title}" status updated to ${carData.status}`, 'success');
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                background: ${type === 'success' ? '#28a745' : '#17a2b8'};
                color: white;
                border-radius: 4px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                z-index: 10000;
                animation: slideIn 0.3s ease-out;
            `;
            notification.textContent = message;
            
            // Add animation
            const style = document.createElement('style');
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
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideIn 0.3s ease-out reverse';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Add data-car-id attribute to each row for easy lookup
        document.querySelectorAll('tbody tr').forEach(row => {
            const checkbox = row.querySelector('.car-checkbox');
            if (checkbox) {
                row.setAttribute('data-car-id', checkbox.value);
            }
        });

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

