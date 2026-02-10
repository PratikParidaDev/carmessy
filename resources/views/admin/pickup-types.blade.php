<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 18px;">Manage Pickup Types</h2>
        <a href="{{ route('admin.pickup-types.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New Pickup Type
        </a>
    </div>

    @if($pickupTypes->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Name</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Display Name</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Sort Order</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Status</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pickupTypes as $pickupType)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;">#{{ $pickupType->id }}</td>
                        <td style="padding: 10px;"><strong>{{ $pickupType->name }}</strong></td>
                        <td style="padding: 10px;">{{ $pickupType->display_name ?? $pickupType->name }}</td>
                        <td style="padding: 10px;">{{ $pickupType->sort_order }}</td>
                        <td style="padding: 10px;">
                            @if($pickupType->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td style="padding: 10px;">
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.pickup-types.edit', $pickupType) }}" 
                                   class="btn btn-sm btn-secondary" 
                                   style="padding: 4px 8px; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.pickup-types.delete', $pickupType) }}" 
                                      method="POST" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this pickup type?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            style="padding: 4px 8px; font-size: 12px;">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @if($pickupType->description)
                    <tr>
                        <td colspan="6" style="padding: 10px; background: #f9f9f9; font-size: 13px; color: #666;">
                            <strong>Description:</strong> {{ $pickupType->description }}
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
                Showing {{ $pickupTypes->firstItem() }} to {{ $pickupTypes->lastItem() }} of {{ $pickupTypes->total() }} pickup types
            </div>
            <div>
                {{ $pickupTypes->links() }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 8px; margin-top: 20px;">
            <i class="fas fa-truck" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #23282d; margin-bottom: 15px;">No Pickup Types</h3>
            <p style="color: #666; font-size: 16px; margin-bottom: 25px;">
                You haven't created any pickup types yet. Create your first pickup type to get started!
            </p>
            <a href="{{ route('admin.pickup-types.create') }}" class="btn btn-primary" style="padding: 12px 24px;">
                <i class="fas fa-plus me-1"></i> Create Pickup Type
            </a>
        </div>
    @endif
</div>

