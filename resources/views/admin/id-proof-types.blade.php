<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 18px;">Manage ID Proof Types</h2>
        <a href="{{ route('admin.id-proof-types.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New ID Proof Type
        </a>
    </div>

    @if($idProofTypes->count() > 0)
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
                    @foreach($idProofTypes as $idProofType)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;">#{{ $idProofType->id }}</td>
                        <td style="padding: 10px;"><strong>{{ $idProofType->name }}</strong></td>
                        <td style="padding: 10px;">{{ $idProofType->display_name ?? $idProofType->name }}</td>
                        <td style="padding: 10px;">{{ $idProofType->sort_order }}</td>
                        <td style="padding: 10px;">
                            @if($idProofType->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td style="padding: 10px;">
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.id-proof-types.edit', $idProofType) }}" 
                                   class="btn btn-sm btn-secondary" 
                                   style="padding: 4px 8px; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.id-proof-types.delete', $idProofType) }}" 
                                      method="POST" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this ID proof type?');">
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
                    @if($idProofType->description)
                    <tr>
                        <td colspan="6" style="padding: 10px; background: #f9f9f9; font-size: 13px; color: #666;">
                            <strong>Description:</strong> {{ $idProofType->description }}
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
                Showing {{ $idProofTypes->firstItem() }} to {{ $idProofTypes->lastItem() }} of {{ $idProofTypes->total() }} ID proof types
            </div>
            <div>
                {{ $idProofTypes->links() }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 8px; margin-top: 20px;">
            <i class="fas fa-id-card" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #23282d; margin-bottom: 15px;">No ID Proof Types</h3>
            <p style="color: #666; font-size: 16px; margin-bottom: 25px;">
                You haven't created any ID proof types yet. Create your first ID proof type to get started!
            </p>
            <a href="{{ route('admin.id-proof-types.create') }}" class="btn btn-primary" style="padding: 12px 24px;">
                <i class="fas fa-plus me-1"></i> Create ID Proof Type
            </a>
        </div>
    @endif
</div>

