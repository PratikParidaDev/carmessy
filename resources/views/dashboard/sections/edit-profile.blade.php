<div class="dashboard-card">
    <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
    
    @if(session('success'))
        <div class="alert alert-success" style="margin-top: 20px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="margin-top: 20px;">
            <strong><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</strong>
            <ul style="margin: 5px 0 0 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="margin-top: 30px;">
        <!-- Update Profile Information Form -->
        <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 25px; margin-bottom: 20px;">
            <h3 style="font-size: 18px; margin-bottom: 20px; color: #23282d; border-bottom: 2px solid #2271b1; padding-bottom: 10px;">
                <i class="fas fa-info-circle"></i> Profile Information
            </h3>

            <form method="POST" action="{{ route('profile.update') }}" class="mt-6">
                @csrf
                @method('patch')
                <input type="hidden" name="dashboard" value="1">

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="name" style="font-weight: 600; color: #23282d; margin-bottom: 8px; display: block;">
                        Full Name <span style="color: red;">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-control" 
                        value="{{ old('name', $user->name) }}" 
                        required 
                        autofocus 
                        autocomplete="name"
                        style="width: 100%; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px;">
                    @error('name')
                        <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="email" style="font-weight: 600; color: #23282d; margin-bottom: 8px; display: block;">
                        Email Address <span style="color: red;">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        value="{{ old('email', $user->email) }}" 
                        required 
                        autocomplete="username"
                        style="width: 100%; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px;">
                    @error('email')
                        <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                    @enderror
                    
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                            <p style="margin: 0; color: #856404; font-size: 13px;">
                                <i class="fas fa-exclamation-triangle"></i> Your email address is unverified.
                                <form method="POST" action="{{ route('verification.send') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: #2271b1; text-decoration: underline; cursor: pointer; padding: 0;">
                                        Click here to re-send the verification email.
                                    </button>
                                </form>
                            </p>
                        </div>
                    @endif
                </div>

                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #ddd;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="{{ route('dashboard.profile') }}" class="btn btn-secondary" style="margin-left: 10px;">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Update Password Form -->
        <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 25px; margin-bottom: 20px;">
            <h3 style="font-size: 18px; margin-bottom: 20px; color: #23282d; border-bottom: 2px solid #2271b1; padding-bottom: 10px;">
                <i class="fas fa-lock"></i> Update Password
            </h3>

            <form method="POST" action="{{ route('password.update') }}" class="mt-6">
                @csrf
                @method('put')
                <input type="hidden" name="dashboard" value="1">

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="current_password" style="font-weight: 600; color: #23282d; margin-bottom: 8px; display: block;">
                        Current Password <span style="color: red;">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        class="form-control" 
                        required 
                        autocomplete="current-password"
                        style="width: 100%; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px;">
                    @error('current_password', 'updatePassword')
                        <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="password" style="font-weight: 600; color: #23282d; margin-bottom: 8px; display: block;">
                        New Password <span style="color: red;">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        required 
                        autocomplete="new-password"
                        style="width: 100%; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px;">
                    @error('password', 'updatePassword')
                        <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="password_confirmation" style="font-weight: 600; color: #23282d; margin-bottom: 8px; display: block;">
                        Confirm New Password <span style="color: red;">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-control" 
                        required 
                        autocomplete="new-password"
                        style="width: 100%; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px;">
                </div>

                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #ddd;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Back Button -->
        <div style="margin-top: 20px;">
            <a href="{{ route('dashboard.profile') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>
</div>

