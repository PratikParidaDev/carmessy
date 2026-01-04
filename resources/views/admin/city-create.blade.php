<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Create New City</h2>
        <a href="{{ route('admin.cities') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Cities
        </a>
    </div>

    <form action="{{ route('admin.cities.store') }}" method="POST">
        @csrf

        <div style="max-width: 600px;">
            <!-- City Name -->
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    City Name <span style="color: red;">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                    placeholder="e.g., Mumbai, Delhi, Bangalore"
                />
                @error('name')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- State -->
            <div style="margin-bottom: 20px;">
                <label for="state" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    State <span style="color: red;">*</span>
                </label>
                <input 
                    type="text" 
                    id="state" 
                    name="state" 
                    value="{{ old('state') }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                    placeholder="e.g., Maharashtra, Delhi, Karnataka"
                />
                @error('state')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Is Popular -->
            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; font-weight: normal; cursor: pointer;">
                    <input 
                        type="checkbox" 
                        id="is_popular" 
                        name="is_popular" 
                        value="1"
                        {{ old('is_popular') ? 'checked' : '' }}
                        style="margin-right: 8px; width: auto;"
                    />
                    <span>Mark as Popular City</span>
                </label>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                    Popular cities appear prominently in search and filters
                </small>
            </div>

            <!-- Is Active -->
            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; font-weight: normal; cursor: pointer;">
                    <input 
                        type="checkbox" 
                        id="is_active" 
                        name="is_active" 
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        style="margin-right: 8px; width: auto;"
                    />
                    <span>Active (visible to users)</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-size: 16px;">
                    <i class="fas fa-save"></i> Create City
                </button>
                <a href="{{ route('admin.cities') }}" class="btn btn-secondary" style="padding: 12px 24px; font-size: 16px; margin-left: 10px;">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

