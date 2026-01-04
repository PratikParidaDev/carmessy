<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Manage Car Models</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.models.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Model
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
            </a>
        </div>
    </div>

    <!-- Filter by Make -->
    <div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
        <form action="{{ route('admin.models') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
            <label for="make_filter" style="margin: 0; font-weight: 500;">Filter by Make:</label>
            <select name="make_id" id="make_filter" class="form-control" style="width: auto; padding: 8px;">
                <option value="">All Makes</option>
                @foreach($makes as $make)
                    <option value="{{ $make->id }}" {{ request('make_id') == $make->id ? 'selected' : '' }}>
                        {{ $make->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request('make_id'))
                <a href="{{ route('admin.models') }}" class="btn btn-secondary">Clear Filter</a>
            @endif
        </form>
    </div>

    @if($models->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Make</th>
                        <th>Model Name</th>
                        <th>Body Type</th>
                        <th>Year Range</th>
                        <th>Cars</th>
                        <th>Active</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($models as $model)
                    <tr>
                        <td>
                            <strong>{{ $model->make->name ?? 'N/A' }}</strong>
                        </td>
                        <td><strong>{{ $model->name }}</strong></td>
                        <td>
                            @if($model->body_type)
                                <span class="badge bg-info">{{ ucfirst($model->body_type) }}</span>
                            @else
                                <span style="color: #999;">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($model->year_start || $model->year_end)
                                {{ $model->year_start ?? '?' }} - {{ $model->year_end ?? 'Present' }}
                            @else
                                <span style="color: #999;">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $model->cars_count ?? 0 }}</span>
                        </td>
                        <td>
                            @if($model->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.models.edit', $model->id) }}" 
                                   class="btn btn-secondary" 
                                   style="padding: 4px 8px; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.models.delete', $model->id) }}" 
                                      method="POST" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete {{ $model->name }}? This will also delete all associated cars.');">
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
                Showing {{ $models->firstItem() }} to {{ $models->lastItem() }} of {{ $models->total() }} models
            </div>
            <div>
                {{ $models->links() }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px;">
            <i class="fas fa-car-side" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <p style="color: #666; font-size: 16px;">No models found.</p>
            @if(request('make_id'))
                <p style="color: #999; font-size: 14px;">Try selecting a different make or <a href="{{ route('admin.models') }}">view all models</a>.</p>
            @else
                <a href="{{ route('admin.models.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                    <i class="fas fa-plus"></i> Add Your First Model
                </a>
            @endif
        </div>
    @endif
</div>

