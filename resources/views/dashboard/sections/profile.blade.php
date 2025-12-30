<div class="dashboard-card">
    <h2><i class="fas fa-user"></i> My Profile</h2>
    
    <div style="margin-top: 30px;">
        <!-- User Information Section -->
        <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 25px; margin-bottom: 20px;">
            <h3 style="font-size: 18px; margin-bottom: 20px; color: #23282d; border-bottom: 2px solid #2271b1; padding-bottom: 10px;">
                <i class="fas fa-info-circle"></i> Account Information
            </h3>
            
            <div class="row" style="margin: 0;">
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Full Name
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        {{ $user->name }}
                    </div>
                </div>
                
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Email Address
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        {{ $user->email }}
                        @if($user->email_verified_at)
                            <span style="color: #46b450; margin-left: 8px;">
                                <i class="fas fa-check-circle"></i> Verified
                            </span>
                        @else
                            <span style="color: #dc3232; margin-left: 8px;">
                                <i class="fas fa-exclamation-circle"></i> Not Verified
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Account Role
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        <span style="background: {{ $user->role === 'admin' ? '#dc3232' : ($user->role === 'dealer' ? '#2271b1' : '#46b450') }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>
                
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Account Status
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        @if($user->is_verified)
                            <span style="color: #46b450;">
                                <i class="fas fa-check-circle"></i> Verified Account
                            </span>
                        @else
                            <span style="color: #dc3232;">
                                <i class="fas fa-times-circle"></i> Unverified Account
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Member Since
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        {{ $user->created_at->format('F j, Y') }}
                        <small style="color: #666; margin-left: 8px;">({{ $user->created_at->diffForHumans() }})</small>
                    </div>
                </div>
                
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Last Updated
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        {{ $user->updated_at->format('F j, Y g:i A') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 25px; margin-bottom: 20px;">
            <h3 style="font-size: 18px; margin-bottom: 20px; color: #23282d; border-bottom: 2px solid #2271b1; padding-bottom: 10px;">
                <i class="fas fa-chart-bar"></i> Account Statistics
            </h3>
            
            <div class="row" style="margin: 0;">
                <div class="col-md-3" style="margin-bottom: 15px;">
                    <div style="background: #f0f6fc; padding: 20px; border-radius: 6px; text-align: center; border-left: 4px solid #2271b1;">
                        <div style="font-size: 32px; font-weight: 700; color: #2271b1; margin-bottom: 5px;">
                            {{ $stats['total_cars'] }}
                        </div>
                        <div style="font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.5px;">
                            Total Cars
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3" style="margin-bottom: 15px;">
                    <div style="background: #f0f9ff; padding: 20px; border-radius: 6px; text-align: center; border-left: 4px solid #46b450;">
                        <div style="font-size: 32px; font-weight: 700; color: #46b450; margin-bottom: 5px;">
                            {{ $stats['favorites_count'] }}
                        </div>
                        <div style="font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.5px;">
                            Favorites
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3" style="margin-bottom: 15px;">
                    <div style="background: #fff3cd; padding: 20px; border-radius: 6px; text-align: center; border-left: 4px solid #ffc107;">
                        <div style="font-size: 32px; font-weight: 700; color: #856404; margin-bottom: 5px;">
                            {{ $stats['reviews_count'] }}
                        </div>
                        <div style="font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.5px;">
                            Reviews
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3" style="margin-bottom: 15px;">
                    <div style="background: #f8d7da; padding: 20px; border-radius: 6px; text-align: center; border-left: 4px solid #dc3545;">
                        <div style="font-size: 32px; font-weight: 700; color: #721c24; margin-bottom: 5px;">
                            {{ $stats['inquiries_count'] }}
                        </div>
                        <div style="font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.5px;">
                            Inquiries
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dealer Information Section (if user has dealer profile) -->
        @if($user->dealer)
        <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 25px; margin-bottom: 20px;">
            <h3 style="font-size: 18px; margin-bottom: 20px; color: #23282d; border-bottom: 2px solid #2271b1; padding-bottom: 10px;">
                <i class="fas fa-store"></i> Dealer Information
            </h3>
            
            <div class="row" style="margin: 0;">
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Business Name
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        {{ $user->dealer->business_name }}
                        @if($user->dealer->is_verified)
                            <span style="color: #46b450; margin-left: 8px;">
                                <i class="fas fa-check-circle"></i> Verified Dealer
                            </span>
                        @endif
                    </div>
                </div>
                
                @if($user->dealer->phone)
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Phone Number
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        {{ $user->dealer->phone }}
                    </div>
                </div>
                @endif
                
                @if($user->dealer->whatsapp)
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        WhatsApp
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        {{ $user->dealer->whatsapp }}
                    </div>
                </div>
                @endif
                
                @if($user->dealer->city)
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Location
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        {{ $user->dealer->city->name }}, {{ $user->dealer->city->state ?? '' }}
                        @if($user->dealer->pincode)
                            - {{ $user->dealer->pincode }}
                        @endif
                    </div>
                </div>
                @endif
                
                @if($user->dealer->address)
                <div class="col-md-12" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Address
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        {{ $user->dealer->address }}
                    </div>
                </div>
                @endif
                
                @if($user->dealer->gst_number)
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        GST Number
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        {{ $user->dealer->gst_number }}
                    </div>
                </div>
                @endif
                
                @if($user->dealer->website)
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Website
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        <a href="{{ $user->dealer->website }}" target="_blank" style="color: #2271b1; text-decoration: none;">
                            {{ $user->dealer->website }}
                            <i class="fas fa-external-link-alt" style="font-size: 12px; margin-left: 5px;"></i>
                        </a>
                    </div>
                </div>
                @endif
                
                @if($user->dealer->description)
                <div class="col-md-12" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Description
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0; line-height: 1.6;">
                        {{ $user->dealer->description }}
                    </div>
                </div>
                @endif
                
                @if($user->dealer->rating)
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Rating
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        <span style="color: #ffc107;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($user->dealer->rating))
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= $user->dealer->rating)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </span>
                        <span style="margin-left: 8px; color: #666;">
                            {{ number_format($user->dealer->rating, 1) }} ({{ $user->dealer->total_reviews }} reviews)
                        </span>
                    </div>
                </div>
                @endif
                
                @if($user->dealer->is_premium)
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block;">
                        Premium Status
                    </label>
                    <div style="font-size: 16px; color: #23282d; padding: 8px 0;">
                        <span style="background: #ffc107; color: #856404; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                            <i class="fas fa-crown"></i> Premium Member
                        </span>
                        @if($user->dealer->premium_until)
                            <small style="color: #666; margin-left: 8px;">
                                Until {{ $user->dealer->premium_until->format('M j, Y') }}
                            </small>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <a href="{{ route('profile.edit', ['dashboard' => 1]) }}" class="btn btn-primary" style="margin-right: 10px;">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

