@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cars.index') }}">Cars</a></li>
                @if($vehicleType === 'car')
                    <li class="breadcrumb-item"><a href="{{ route('cars.show', $vehicle->slug) }}">{{ $vehicle->make->name }} {{ $vehicle->model->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">Book Now</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Book Vehicle</h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Vehicle Info -->
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading">Vehicle Details</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Vehicle:</strong> {{ $vehicle->make->name }} {{ $vehicle->model->name }}</p>
                                    <p class="mb-1"><strong>Year:</strong> {{ $vehicle->year }}</p>
                                    <p class="mb-1"><strong>Price:</strong> ₹{{ number_format($vehicle->price, 0) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Condition:</strong> {{ ucfirst($vehicle->condition) }}</p>
                                    <p class="mb-1"><strong>Location:</strong> {{ $vehicle->city->name }}</p>
                                    <p class="mb-0"><strong>Mileage:</strong> {{ number_format($vehicle->mileage) }} km</p>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Form -->
                        <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                            @csrf
                            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                            <input type="hidden" name="vehicle_type" value="{{ $vehicleType }}">

                            <div class="row g-3">
                                <!-- Full Name -->
                                <div class="col-md-6">
                                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                           id="full_name" name="full_name" 
                                           value="{{ old('full_name', $user->name) }}" required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone Number -->
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" name="phone_number" 
                                           value="{{ old('phone_number') }}" 
                                           placeholder="+91 9876543210" required>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- City -->
                                <div class="col-md-6">
                                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" 
                                           value="{{ old('city') }}" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Preferred Booking Date -->
                                <div class="col-md-6">
                                    <label for="preferred_booking_date" class="form-label">Preferred Booking Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('preferred_booking_date') is-invalid @enderror" 
                                           id="preferred_booking_date" name="preferred_booking_date" 
                                           value="{{ old('preferred_booking_date') }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                    @error('preferred_booking_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Preferred Time Slot -->
                                <div class="col-md-6">
                                    <label for="preferred_time_slot" class="form-label">Preferred Time Slot <span class="text-danger">*</span></label>
                                    <select class="form-select @error('preferred_time_slot') is-invalid @enderror" 
                                            id="preferred_time_slot" name="preferred_time_slot" required>
                                        <option value="">Select Time Slot</option>
                                        @forelse($timeSlots as $timeSlot)
                                            <option value="{{ $timeSlot->name }}" {{ old('preferred_time_slot') == $timeSlot->name ? 'selected' : '' }}>
                                                {{ $timeSlot->name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No time slots available. Please contact admin.</option>
                                        @endforelse
                                    </select>
                                    @error('preferred_time_slot')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($timeSlots->isEmpty())
                                        <small class="text-danger">No active time slots available. Please contact administrator.</small>
                                    @endif
                                </div>

                                <!-- Pickup Type -->
                                <div class="col-md-6">
                                    <label for="pickup_type" class="form-label">Pickup Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('pickup_type') is-invalid @enderror" 
                                            id="pickup_type" name="pickup_type" required>
                                        <option value="">Select Pickup Type</option>
                                        @forelse($pickupTypes as $pickupType)
                                            <option value="{{ $pickupType->slug }}" {{ old('pickup_type') == $pickupType->slug ? 'selected' : '' }}>
                                                {{ $pickupType->display_name ?? $pickupType->name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No pickup types available. Please contact admin.</option>
                                        @endforelse
                                    </select>
                                    @error('pickup_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($pickupTypes->isEmpty())
                                        <small class="text-danger">No active pickup types available. Please contact administrator.</small>
                                    @endif
                                </div>

                                <!-- Payment Mode -->
                                <div class="col-md-6">
                                    <label for="payment_mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                    <select class="form-select @error('payment_mode') is-invalid @enderror" 
                                            id="payment_mode" name="payment_mode" required>
                                        <option value="">Select Payment Mode</option>
                                        @forelse($paymentModes as $paymentMode)
                                            <option value="{{ $paymentMode->slug }}" {{ old('payment_mode') == $paymentMode->slug ? 'selected' : '' }}>
                                                {{ $paymentMode->display_name ?? $paymentMode->name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No payment modes available. Please contact admin.</option>
                                        @endforelse
                                    </select>
                                    @error('payment_mode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($paymentModes->isEmpty())
                                        <small class="text-danger">No active payment modes available. Please contact administrator.</small>
                                    @endif
                                </div>

                                <!-- ID Proof Type -->
                                <div class="col-md-6">
                                    <label for="id_proof_type" class="form-label">ID Proof Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('id_proof_type') is-invalid @enderror" 
                                            id="id_proof_type" name="id_proof_type" required>
                                        <option value="">Select ID Proof Type</option>
                                        @forelse($idProofTypes as $idProofType)
                                            <option value="{{ $idProofType->slug }}" {{ old('id_proof_type') == $idProofType->slug ? 'selected' : '' }}>
                                                {{ $idProofType->display_name ?? $idProofType->name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No ID proof types available. Please contact admin.</option>
                                        @endforelse
                                    </select>
                                    @error('id_proof_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($idProofTypes->isEmpty())
                                        <small class="text-danger">No active ID proof types available. Please contact administrator.</small>
                                    @endif
                                </div>

                                <!-- ID Proof Number -->
                                <div class="col-md-6">
                                    <label for="id_proof_number" class="form-label">ID Proof Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('id_proof_number') is-invalid @enderror" 
                                           id="id_proof_number" name="id_proof_number" 
                                           value="{{ old('id_proof_number') }}" 
                                           placeholder="Enter ID Proof Number" required>
                                    @error('id_proof_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Message / Special Request -->
                                <div class="col-12">
                                    <label for="message" class="form-label">Message / Special Request (Optional)</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" 
                                              id="message" name="message" 
                                              rows="4" 
                                              placeholder="Any special requests or additional information...">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="{{ $vehicleType === 'car' ? route('cars.show', $vehicle->slug) : route('cars.index') }}" 
                                           class="btn btn-outline-secondary me-md-2">
                                            <i class="fas fa-arrow-left me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-check-circle me-1"></i> Submit Booking
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Set minimum date to today
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('preferred_booking_date').setAttribute('min', today);
    });
</script>
@endpush
@endsection

