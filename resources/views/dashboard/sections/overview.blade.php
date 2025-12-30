@if(isset($stats))
<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Cars</h3>
        <div class="stat-value">{{ $stats['total_cars'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Pending</h3>
        <div class="stat-value">{{ $stats['pending_cars'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Approved</h3>
        <div class="stat-value">{{ $stats['approved_cars'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Rejected</h3>
        <div class="stat-value">{{ $stats['rejected_cars'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Sold</h3>
        <div class="stat-value">{{ $stats['sold_cars'] }}</div>
    </div>
</div>
@endif

<div class="dashboard-card">
    <h2>Welcome to Your Dashboard</h2>
    <p>You're logged in as <strong>{{ auth()->user()->name }}</strong> ({{ ucfirst(auth()->user()->role) }})</p>
    
    @if(auth()->user()->isDealer() || auth()->user()->dealer || auth()->user()->isAdmin())
        <!-- Show car management for dealers, admins, and buyers who have posted cars -->
        @if(isset($listingFields) && $cars && $cars->count() > 0)
            <!-- WordPress-style All Cars Table -->
            <div style="margin-top: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 18px;">
                        @if(auth()->user()->isAdmin())
                            All Cars (Admin View)
                        @else
                            All Cars
                        @endif
                    </h2>
                    <a href="{{ route('dashboard.cars.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                </div>

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
                                @if(auth()->user()->isAdmin())
                                    <th style="width: 150px;">Dealer</th>
                                @endif
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cars as $car)
                            <tr>
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
                                @if(auth()->user()->isAdmin())
                                    <td>
                                        <small style="color: #666;">
                                            Dealer: {{ $car->dealer->business_name ?? 'N/A' }}
                                        </small>
                                    </td>
                                @endif
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
            </div>
        @else
            @if(auth()->user()->isAdmin())
                <p style="margin-top: 20px;">No cars found in the system.</p>
            @elseif(auth()->user()->isDealer() || auth()->user()->dealer)
                <p style="margin-top: 20px;">You haven't added any cars yet. <a href="{{ route('dashboard.cars.create') }}">Add your first car</a></p>
            @endif
        @endif
    @else
        <!-- Buyer/Seller View - No CRUD Operations -->
        <div style="margin-top: 30px; padding: 30px; background: #f9f9f9; border-radius: 8px; text-align: center;">
            <i class="fas fa-car" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #23282d; margin-bottom: 15px;">Welcome to Car Marketplace</h3>
            <p style="color: #666; font-size: 16px; margin-bottom: 25px;">
                As a {{ ucfirst(auth()->user()->role) }}, you can browse and search for cars, save favorites, and contact dealers.
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('cars.index') }}" class="btn btn-primary" style="padding: 12px 24px;">
                    <i class="fas fa-search"></i> Browse Cars
                </a>
                <a href="{{ route('favorites.index') }}" class="btn btn-secondary" style="padding: 12px 24px;">
                    <i class="fas fa-heart"></i> My Favorites
                </a>
                <a href="{{ route('dashboard.cars.create') }}" class="btn btn-primary" style="padding: 12px 24px; background: #28a745; border-color: #28a745;">
                    <i class="fas fa-plus-circle"></i> Post Your Car
                </a>
            </div>
            <div style="margin-top: 30px; padding: 20px; background: #fff; border-left: 4px solid #28a745; border-radius: 4px; text-align: left;">
                <h4 style="color: #23282d; margin-bottom: 10px;">
                    <i class="fas fa-info-circle" style="color: #28a745;"></i> Sell Your Used Car
                </h4>
                <p style="color: #666; margin: 0; font-size: 14px;">
                    List your used car for sale! Click "Post Your Car" above to create your listing. A dealer profile will be automatically created for you when you post your first car.
                </p>
            </div>
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
    });
</script>
