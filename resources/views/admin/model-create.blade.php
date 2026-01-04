<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Create New Model</h2>
        <a href="{{ route('admin.models') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Models
        </a>
    </div>

    <form action="{{ route('admin.models.store') }}" method="POST">
        @csrf

        <div style="max-width: 600px;">
            <!-- Make -->
            <div style="margin-bottom: 20px;">
                <label for="make_id" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Make <span style="color: red;">*</span>
                </label>
                <select 
                    id="make_id" 
                    name="make_id" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                >
                    <option value="">Select Make</option>
                    @foreach($makes as $make)
                        <option value="{{ $make->id }}" {{ old('make_id') == $make->id ? 'selected' : '' }}>
                            {{ $make->name }}
                        </option>
                    @endforeach
                </select>
                @error('make_id')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Model Name -->
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Model Name <span style="color: red;">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                    placeholder="e.g., Camry, Civic, F-150"
                />
                @error('name')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Body Type -->
            <div style="margin-bottom: 20px;">
                <label for="body_type" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Body Type
                </label>
                <select 
                    id="body_type" 
                    name="body_type" 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                >
                    <option value="">Select Body Type</option>
                    <option value="sedan" {{ old('body_type') == 'sedan' ? 'selected' : '' }}>Sedan</option>
                    <option value="suv" {{ old('body_type') == 'suv' ? 'selected' : '' }}>SUV</option>
                    <option value="hatchback" {{ old('body_type') == 'hatchback' ? 'selected' : '' }}>Hatchback</option>
                    <option value="coupe" {{ old('body_type') == 'coupe' ? 'selected' : '' }}>Coupe</option>
                    <option value="convertible" {{ old('body_type') == 'convertible' ? 'selected' : '' }}>Convertible</option>
                    <option value="wagon" {{ old('body_type') == 'wagon' ? 'selected' : '' }}>Wagon</option>
                    <option value="van" {{ old('body_type') == 'van' ? 'selected' : '' }}>Van</option>
                    <option value="truck" {{ old('body_type') == 'truck' ? 'selected' : '' }}>Truck</option>
                    <option value="luxury" {{ old('body_type') == 'luxury' ? 'selected' : '' }}>Luxury</option>
                </select>
                @error('body_type')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Year Range -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 500; color: #333;">
                    Year Range (Optional)
                </label>
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label for="year_start" style="display: block; margin-bottom: 5px; font-size: 13px; color: #666;">
                            Start Year
                        </label>
                        <input 
                            type="number" 
                            id="year_start" 
                            name="year_start" 
                            value="{{ old('year_start') }}" 
                            min="1900"
                            max="{{ date('Y') + 1 }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                            placeholder="e.g., 2015"
                        />
                    </div>
                    <div style="flex: 1;">
                        <label for="year_end" style="display: block; margin-bottom: 5px; font-size: 13px; color: #666;">
                            End Year
                        </label>
                        <input 
                            type="number" 
                            id="year_end" 
                            name="year_end" 
                            value="{{ old('year_end') }}" 
                            min="1900"
                            max="{{ date('Y') + 10 }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                            placeholder="e.g., 2025 or leave blank"
                        />
                    </div>
                </div>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                    Leave blank if the model is still in production
                </small>
                @error('year_start')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
                @error('year_end')
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
                        {{ old('is_active', true) ? 'checked' : '' }}
                        style="margin-right: 8px; width: auto;"
                    />
                    <span>Active (visible to users)</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-size: 16px;">
                    <i class="fas fa-save"></i> Create Model
                </button>
                <a href="{{ route('admin.models') }}" class="btn btn-secondary" style="padding: 12px 24px; font-size: 16px; margin-left: 10px;">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

