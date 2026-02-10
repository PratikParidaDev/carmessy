<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 18px;">Create New Pickup Type</h2>
        <a href="{{ route('admin.pickup-types') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Pickup Types
        </a>
    </div>

    <form action="{{ route('admin.pickup-types.store') }}" method="POST">
        @csrf
        
        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="e.g., Showroom, Home Delivery"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">This is the value stored in database (slug will be auto-generated)</small>
            </div>

            <div class="col-md-6">
                <label for="display_name" class="form-label">Display Name</label>
                <input type="text" 
                       class="form-control @error('display_name') is-invalid @enderror" 
                       id="display_name" 
                       name="display_name" 
                       value="{{ old('display_name') }}" 
                       placeholder="e.g., Showroom Pickup">
                @error('display_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Custom display name (optional, defaults to name)</small>
            </div>

            <div class="col-md-6">
                <label for="sort_order" class="form-label">Sort Order</label>
                <input type="number" 
                       class="form-control @error('sort_order') is-invalid @enderror" 
                       id="sort_order" 
                       name="sort_order" 
                       value="{{ old('sort_order', 0) }}" 
                       min="0">
                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Lower numbers appear first in the dropdown</small>
            </div>

            <div class="col-md-6">
                <div class="form-check mt-4">
                    <input class="form-check-input" 
                           type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active (Visible to users)
                    </label>
                </div>
            </div>

            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="3"
                          placeholder="Optional description for this pickup type">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create Pickup Type
            </button>
            <a href="{{ route('admin.pickup-types') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

