<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Edit Admin: {{ $editAdmin->name }}</h2>
        <a href="{{ route('super-admin.admins') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Admins
        </a>
    </div>

    <form action="{{ route('super-admin.admins.update', $editAdmin->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="max-width: 600px;">
            <!-- Name -->
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Name <span style="color: red;">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $editAdmin->name) }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
                @error('name')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Email <span style="color: red;">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $editAdmin->email) }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
                @error('email')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    minlength="8"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                    Leave blank to keep current password. Minimum 8 characters if changing.
                </small>
                @error('password')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div style="margin-bottom: 20px;">
                <label for="password_confirmation" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Confirm Password
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    minlength="8"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
            </div>

            <!-- Role -->
            <div style="margin-bottom: 20px;">
                <label for="role" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Role <span style="color: red;">*</span>
                </label>
                <select 
                    id="role" 
                    name="role" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                >
                    <option value="admin" {{ old('role', $editAdmin->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ old('role', $editAdmin->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                @error('role')
                    <span style="color: red; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-size: 16px;">
                    <i class="fas fa-save"></i> Update Admin
                </button>
                <a href="{{ route('super-admin.admins') }}" class="btn btn-secondary" style="padding: 12px 24px; font-size: 16px; margin-left: 10px;">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

