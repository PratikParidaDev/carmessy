<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Edit Feature: {{ $editFeature->name }}</h2>
        <a href="{{ route('admin.features') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Features
        </a>
    </div>

    <form action="{{ route('admin.features.update', $editFeature->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="max-width: 600px;">
            <!-- Feature Name -->
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Feature Name <span style="color: red;">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $editFeature->name) }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
                @error('name')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div style="margin-bottom: 20px;">
                <label for="description" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="3"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                >{{ old('description', $editFeature->description) }}</textarea>
                @error('description')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Icon -->
            <div style="margin-bottom: 20px;">
                <label for="icon" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Icon (FontAwesome class)
                </label>
                <input 
                    type="text" 
                    id="icon" 
                    name="icon" 
                    value="{{ old('icon', $editFeature->icon) }}" 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
                @error('icon')
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
                    value="{{ old('order', $editFeature->order) }}" 
                    min="0"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
                @error('order')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Is Active -->
            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; font-weight: normal; cursor: pointer;">
                    <input 
                        type="checkbox" 
                        id="is_active" 
                        name="is_active" 
                        value="1"
                        {{ old('is_active', $editFeature->is_active) ? 'checked' : '' }}
                        style="margin-right: 8px; width: auto;"
                    />
                    <span>Active (visible to users)</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-size: 16px;">
                    <i class="fas fa-save"></i> Update Feature
                </button>
                <a href="{{ route('admin.features') }}" class="btn btn-secondary" style="padding: 12px 24px; font-size: 16px; margin-left: 10px;">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

