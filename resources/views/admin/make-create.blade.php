<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Create New Make</h2>
        <a href="{{ route('admin.makes') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Makes
        </a>
    </div>

    <form action="{{ route('admin.makes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="max-width: 600px;">
            <!-- Name -->
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Make Name <span style="color: red;">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                    placeholder="e.g., Toyota, Honda, Ford"
                />
                @error('name')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Country -->
            <div style="margin-bottom: 20px;">
                <label for="country" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Country
                </label>
                <input 
                    type="text" 
                    id="country" 
                    name="country" 
                    value="{{ old('country') }}" 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                    placeholder="e.g., Japan, USA, Germany"
                />
                @error('country')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Logo -->
            <div style="margin-bottom: 20px;">
                <label for="logo" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Logo
                </label>
                <input 
                    type="file" 
                    id="logo" 
                    name="logo" 
                    accept="image/*"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                    Recommended: Square image, max 2MB (JPEG, PNG, WEBP)
                </small>
                @error('logo')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Order -->
            <div style="margin-bottom: 20px;">
                <label for="order" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Display Order
                </label>
                <input 
                    type="number" 
                    id="order" 
                    name="order" 
                    value="{{ old('order', 0) }}" 
                    min="0"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                    Lower numbers appear first (0 = default)
                </small>
                @error('order')
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
                    <span>Mark as Popular Make</span>
                </label>
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
                    <i class="fas fa-save"></i> Create Make
                </button>
                <a href="{{ route('admin.makes') }}" class="btn btn-secondary" style="padding: 12px 24px; font-size: 16px; margin-left: 10px;">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

