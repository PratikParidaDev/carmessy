<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Manage Car Makes</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.makes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Make
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
            </a>
        </div>
    </div>

    @if($makes->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="width: 60px;">Logo</th>
                        <th>Name</th>
                        <th>Country</th>
                        <th>Models</th>
                        <th>Cars</th>
                        <th>Popular</th>
                        <th>Active</th>
                        <th>Order</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($makes as $make)
                    <tr>
                        <td>
                            @if($make->logo)
                                <img src="{{ asset('storage/' . $make->logo) }}" 
                                     alt="{{ $make->name }}" 
                                     style="width: 50px; height: 50px; object-fit: contain; border-radius: 4px;">
                            @else
                                <div style="width: 50px; height: 50px; background: #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-car" style="color: #999;"></i>
                                </div>
                            @endif
                        </td>
                        <td><strong>{{ $make->name }}</strong></td>
                        <td>{{ $make->country ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $make->models_count ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $make->cars_count ?? 0 }}</span>
                        </td>
                        <td>
                            @if($make->is_popular)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            @if($make->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $make->order }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.makes.edit', $make->id) }}" 
                                   class="btn btn-secondary" 
                                   style="padding: 4px 8px; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.makes.delete', $make->id) }}" 
                                      method="POST" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete {{ $make->name }}? This will also delete all associated models and cars.');">
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
                Showing {{ $makes->firstItem() }} to {{ $makes->lastItem() }} of {{ $makes->total() }} makes
            </div>
            <div>
                {{ $makes->links() }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px;">
            <i class="fas fa-industry" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <p style="color: #666; font-size: 16px;">No makes found.</p>
            <a href="{{ route('admin.makes.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                <i class="fas fa-plus"></i> Add Your First Make
            </a>
        </div>
    @endif
</div>

