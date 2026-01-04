<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Manage Cities</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.cities.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New City
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
            </a>
        </div>
    </div>

    @if($cities->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>City Name</th>
                        <th>State</th>
                        <th>Popular</th>
                        <th>Active</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cities as $city)
                    <tr>
                        <td><strong>{{ $city->name }}</strong></td>
                        <td>{{ $city->state }}</td>
                        <td>
                            @if($city->is_popular)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            @if($city->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.cities.edit', $city->id) }}" 
                                   class="btn btn-secondary" 
                                   style="padding: 4px 8px; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.cities.delete', $city->id) }}" 
                                      method="POST" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete {{ $city->name }}? This action cannot be undone if the city has associated cars or dealers.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-danger" 
                                            style="padding: 4px 8px; font-size: 12px;">
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

        <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div style="color: #666; font-size: 13px;">
                Showing {{ $cities->firstItem() }} to {{ $cities->lastItem() }} of {{ $cities->total() }} cities
            </div>
            <div>
                {{ $cities->links() }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px;">
            <i class="fas fa-map-marker-alt" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <p style="color: #666; font-size: 16px;">No cities found.</p>
            <a href="{{ route('admin.cities.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                <i class="fas fa-plus"></i> Add Your First City
            </a>
        </div>
    @endif
</div>

