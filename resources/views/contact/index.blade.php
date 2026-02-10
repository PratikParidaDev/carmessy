@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/contact.css') }}">

<div class="container-fluid contact-page-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    <!-- Contact Information -->
                    <div class="col-lg-5 mb-4 mb-lg-0">
                        <div class="card shadow-lg contact-info-card">
                            <div class="card-body contact-info-body">
                                <h2 class="contact-info-title">
                                    <i class="fas fa-envelope-open-text me-2"></i>Get In Touch
                                </h2>
                                <p class="contact-info-text">
                                    Have questions or need assistance? We're here to help! Reach out to us through any of the following channels.
                                </p>
                                
                                <div class="contact-info">
                                    <div class="contact-info-item">
                                        <div class="contact-icon-box">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div>
                                            <h5>Address</h5>
                                            <p>
                                                123 Car Market Street,<br>
                                                New Delhi, India - 110001
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="contact-info-item">
                                        <div class="contact-icon-box">
                                            <i class="fas fa-phone-alt"></i>
                                        </div>
                                        <div>
                                            <h5>Phone</h5>
                                            <p>
                                                <a href="tel:+911234567890">+91 123 456 7890</a><br>
                                                <a href="tel:+919876543210">+91 987 654 3210</a>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="contact-info-item">
                                        <div class="contact-icon-box">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div>
                                            <h5>Email</h5>
                                            <p>
                                                <a href="mailto:info@carmarketplace.com">info@carmarketplace.com</a><br>
                                                <a href="mailto:support@carmarketplace.com">support@carmarketplace.com</a>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="contact-info-item">
                                        <div class="contact-icon-box">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <h5>Business Hours</h5>
                                            <p>
                                                Monday - Friday: 9:00 AM - 6:00 PM<br>
                                                Saturday: 10:00 AM - 4:00 PM<br>
                                                Sunday: Closed
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h5 class="mb-3 contact-social-title">Follow Us</h5>
                                    <a href="#" class="social-link-icon">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="social-link-icon">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="social-link-icon">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                    <a href="#" class="social-link-icon">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Form -->
                    <div class="col-lg-7">
                        <div class="card shadow-lg contact-form-card">
                            <div class="card-body contact-form-body">
                                <h2 class="contact-form-title">
                                    <i class="fas fa-paper-plane me-2 text-primary"></i>Send Us a Message
                                </h2>
                                <p class="contact-form-subtitle">Fill out the form below and we'll get back to you as soon as possible.</p>
                                
                                <form action="{{ route('contact.store') }}" method="POST" id="contactForm">
                                    @csrf
                                    
                                    <div class="row g-3">
                                        <!-- Name -->
                                        <div class="col-md-6">
                                            <label for="name" class="form-label">
                                                Full Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}" 
                                                   placeholder="Enter your full name"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Email -->
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">
                                                Email Address <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" 
                                                   placeholder="Enter your email"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Phone -->
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">
                                                Phone Number <span class="text-muted">(Optional)</span>
                                            </label>
                                            <input type="tel" 
                                                   class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" 
                                                   name="phone" 
                                                   value="{{ old('phone') }}" 
                                                   placeholder="+91 9876543210">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Subject -->
                                        <div class="col-md-6">
                                            <label for="subject" class="form-label">
                                                Subject <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('subject') is-invalid @enderror" 
                                                   id="subject" 
                                                   name="subject" 
                                                   value="{{ old('subject') }}" 
                                                   placeholder="What is this regarding?"
                                                   required>
                                            @error('subject')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Message -->
                                        <div class="col-12">
                                            <label for="message" class="form-label">
                                                Message <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                                      id="message" 
                                                      name="message" 
                                                      rows="6" 
                                                      placeholder="Tell us how we can help you..."
                                                      required>{{ old('message') }}</textarea>
                                            @error('message')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Submit Button -->
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-lg contact-submit-btn">
                                                <i class="fas fa-paper-plane me-2"></i>Send Message
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/contact.js') }}" defer></script>
@endsection
