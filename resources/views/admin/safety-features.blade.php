<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Manage Safety Features</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.safety-features.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Safety Feature
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
            </a>
        </div>
    </div>

    @if($safetyFeatures->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Icon</th>
                        <th>Order</th>
                        <th>Active</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($safetyFeatures as $safetyFeature)
                    <tr>
                        <td><strong>{{ $safetyFeature->name }}</strong></td>
                        <td>
                            @if($safetyFeature->description)
                                <span style="color: #666; font-size: 13px;">{{ Str::limit($safetyFeature->description, 50) }}</span>
                            @else
                                <span style="color: #999;">No description</span>
                            @endif
                        </td>
                        <td>
                            @if($safetyFeature->icon)
                                <i class="{{ $safetyFeature->icon }}" style="font-size: 18px; color: #2271b1;"></i>
                            @else
                                <span style="color: #999;">-</span>
                            @endif
                        </td>
                        <td>{{ $safetyFeature->order }}</td>
                        <td>
                            @if($safetyFeature->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.safety-features.edit', $safetyFeature->id) }}" 
                                   class="btn btn-secondary" 
                                   style="padding: 4px 8px; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.safety-features.delete', $safetyFeature->id) }}" 
                                      method="POST" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete {{ $safetyFeature->name }}?');">
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
                Showing {{ $safetyFeatures->firstItem() }} to {{ $safetyFeatures->lastItem() }} of {{ $safetyFeatures->total() }} safety features
            </div>
            <div>
                {{ $safetyFeatures->links() }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px;">
            <i class="fas fa-shield-alt" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <p style="color: #666; font-size: 16px;">No safety features found.</p>
            <a href="{{ route('admin.safety-features.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                <i class="fas fa-plus"></i> Add Your First Safety Feature
            </a>
        </div>
    @endif
</div>

