<x-guest-layout>
    <div style="max-width: 400px; margin: 50px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="color: #333; margin-bottom: 10px;">Admin Login</h2>
            <p style="color: #666; font-size: 14px;">Access the admin dashboard</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <!-- Email Address -->
            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">
                    Email Address
                </label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    autocomplete="username"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">
                    Password
                </label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember" 
                        style="margin-right: 8px;"
                    />
                    <span style="color: #666; font-size: 14px;">Remember me</span>
                </label>
            </div>

            <div style="margin-top: 25px;">
                <button 
                    type="submit" 
                    style="width: 100%; padding: 12px; background: #2271b1; color: white; border: none; border-radius: 4px; font-size: 16px; font-weight: 500; cursor: pointer; transition: background 0.2s;"
                    onmouseover="this.style.background='#135e96'"
                    onmouseout="this.style.background='#2271b1'"
                >
                    Log in
                </button>
            </div>

            <div style="margin-top: 20px; text-align: center;">
                <a href="{{ route('login') }}" style="color: #2271b1; text-decoration: none; font-size: 14px;">
                    Regular User Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>

