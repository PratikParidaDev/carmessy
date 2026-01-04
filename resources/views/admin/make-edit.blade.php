<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Edit Make: {{ $editMake->name }}</h2>
        <a href="{{ route('admin.makes') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Makes
        </a>
    </div>

    <form action="{{ route('admin.makes.update', $editMake->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                    value="{{ old('name', $editMake->name) }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
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
                    value="{{ old('country', $editMake->country) }}" 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
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
                @if($editMake->logo)
                    <div style="margin-bottom: 10px;">
                        <img src="{{ asset('storage/' . $editMake->logo) }}" 
                             alt="{{ $editMake->name }}" 
                             style="width: 100px; height: 100px; object-fit: contain; border: 1px solid #ddd; border-radius: 4px; padding: 5px;">
                        <p style="font-size: 12px; color: #666; margin-top: 5px;">Current logo</p>
                    </div>
                @endif
                <input 
                    type="file" 
                    id="logo" 
                    name="logo" 
                    accept="image/*"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                    Leave blank to keep current logo. Recommended: Square image, max 2MB
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
                    value="{{ old('order', $editMake->order) }}" 
                    min="0"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
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
                        {{ old('is_popular', $editMake->is_popular) ? 'checked' : '' }}
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
                        {{ old('is_active', $editMake->is_active) ? 'checked' : '' }}
                        style="margin-right: 8px; width: auto;"
                    />
                    <span>Active (visible to users)</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-size: 16px;">
                    <i class="fas fa-save"></i> Update Make
                </button>
                <a href="{{ route('admin.makes') }}" class="btn btn-secondary" style="padding: 12px 24px; font-size: 16px; margin-left: 10px;">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

