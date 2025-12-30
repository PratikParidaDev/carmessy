<div class="dashboard-card">
    <h2>
        @if(auth()->user()->isBuyer() || (!auth()->user()->isDealer() && !auth()->user()->isAdmin() && !auth()->user()->dealer))
            Post Your Used Car for Sale
        @else
            Add New Car
        @endif
    </h2>
    
    @if(auth()->user()->isBuyer() || (!auth()->user()->isDealer() && !auth()->user()->isAdmin() && !auth()->user()->dealer))
        <div class="alert" style="background: #d1ecf1; border-color: #bee5eb; color: #0c5460; margin-bottom: 20px;">
            <strong><i class="fas fa-info-circle"></i> Note:</strong> A dealer profile will be automatically created for you when you submit your first car listing. Your car will be pending admin approval before it goes live.
        </div>
    @endif

    <form id="car-form" action="{{ route('dashboard.cars.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="row">
            @php
                $fieldGroups = [
                    'basic' => ['title', 'year', 'make_id', 'model_id', 'city_id', 'price'],
                    'specifications' => ['condition', 'fuel_type', 'transmission', 'mileage', 'engine_capacity', 'power', 'torque', 'mileage_kmpl'],
                    'appearance' => ['exterior_color', 'interior_color', 'seats', 'doors'],
                    'features' => ['features', 'safety_features'],
                    'ownership' => ['owners', 'insurance_valid', 'insurance_expiry', 'under_warranty', 'service_history'],
                    'description' => ['description'],
                    'media' => ['images'],
                ];
            @endphp

            @foreach($fieldGroups as $groupName => $groupFields)
                @if($groupName === 'basic')
                    <div class="row">
                        @foreach($groupFields as $fieldName)
                            @if(isset($fields[$fieldName]))
                                <div class="col-md-{{ in_array($fieldName, ['title', 'year']) ? '6' : (in_array($fieldName, ['make_id', 'model_id', 'city_id', 'price']) ? '6' : '12') }}">
                                    <x-dynamic-form-field 
                                        :field="$fields[$fieldName]" 
                                        :makes="$makes" 
                                        :cities="$cities" />
                                </div>
                            @endif
                        @endforeach
                    </div>
                @elseif($groupName === 'specifications')
                    <h3 style="font-size: 16px; margin-top: 20px; margin-bottom: 15px; color: #23282d; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
                        Specifications
                    </h3>
                    <div class="row">
                        @foreach($groupFields as $fieldName)
                            @if(isset($fields[$fieldName]))
                                <div class="col-md-{{ in_array($fieldName, ['condition', 'fuel_type', 'transmission']) ? '4' : '6' }}">
                                    <x-dynamic-form-field 
                                        :field="$fields[$fieldName]" 
                                        :makes="$makes" 
                                        :cities="$cities" />
                                </div>
                            @endif
                        @endforeach
                    </div>
                @elseif($groupName === 'appearance')
                    <h3 style="font-size: 16px; margin-top: 20px; margin-bottom: 15px; color: #23282d; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
                        Appearance
                    </h3>
                    <div class="row">
                        @foreach($groupFields as $fieldName)
                            @if(isset($fields[$fieldName]))
                                <div class="col-md-{{ in_array($fieldName, ['exterior_color', 'interior_color']) ? '6' : '6' }}">
                                    <x-dynamic-form-field 
                                        :field="$fields[$fieldName]" 
                                        :makes="$makes" 
                                        :cities="$cities" />
                                </div>
                            @endif
                        @endforeach
                    </div>
                @elseif($groupName === 'features')
                    <h3 style="font-size: 16px; margin-top: 20px; margin-bottom: 15px; color: #23282d; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
                        Features & Safety
                    </h3>
                    <div class="row">
                        @foreach($groupFields as $fieldName)
                            @if(isset($fields[$fieldName]))
                                <div class="col-md-6">
                                    <x-dynamic-form-field 
                                        :field="$fields[$fieldName]" 
                                        :makes="$makes" 
                                        :cities="$cities" />
                                </div>
                            @endif
                        @endforeach
                    </div>
                @elseif($groupName === 'ownership')
                    <h3 style="font-size: 16px; margin-top: 20px; margin-bottom: 15px; color: #23282d; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
                        Ownership & History
                    </h3>
                    <div class="row">
                        @foreach($groupFields as $fieldName)
                            @if(isset($fields[$fieldName]))
                                <div class="col-md-{{ in_array($fieldName, ['owners']) ? '6' : (in_array($fieldName, ['insurance_valid', 'under_warranty']) ? '6' : '12') }}">
                                    <x-dynamic-form-field 
                                        :field="$fields[$fieldName]" 
                                        :makes="$makes" 
                                        :cities="$cities" />
                                </div>
                            @endif
                        @endforeach
                    </div>
                @elseif($groupName === 'description')
                    <h3 style="font-size: 16px; margin-top: 20px; margin-bottom: 15px; color: #23282d; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
                        Description
                    </h3>
                    @if(isset($fields['description']))
                        <x-dynamic-form-field 
                            :field="$fields['description']" 
                            :makes="$makes" 
                            :cities="$cities" />
                    @endif
                @elseif($groupName === 'media')
                    <h3 style="font-size: 16px; margin-top: 20px; margin-bottom: 15px; color: #23282d; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
                        Images
                    </h3>
                    @if(isset($fields['images']))
                        <x-dynamic-form-field 
                            :field="$fields['images']" 
                            :makes="$makes" 
                            :cities="$cities" />
                    @endif
                @endif
            @endforeach
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> 
                @if(auth()->user()->isBuyer() || (!auth()->user()->isDealer() && !auth()->user()->isAdmin() && !auth()->user()->dealer))
                    Post Car for Sale
                @else
                    Submit Car Listing
                @endif
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
    /* Validation Styles */
    .form-group input.is-invalid,
    .form-group select.is-invalid,
    .form-group textarea.is-invalid {
        border-color: #dc3232 !important;
        box-shadow: 0 0 0 1px #dc3232 !important;
    }

    .form-group input.is-valid,
    .form-group select.is-valid,
    .form-group textarea.is-valid {
        border-color: #46b450 !important;
    }

    .validation-error {
        color: #dc3232;
        font-size: 13px;
        margin-top: 5px;
        display: block;
        font-weight: 500;
    }

    .validation-success {
        color: #46b450;
        font-size: 13px;
        margin-top: 5px;
        display: block;
    }

    /* Server-side error display */
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        padding: 12px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .alert-danger ul {
        margin: 5px 0 0 0;
        padding-left: 20px;
    }
</style>

@if($errors->any())
    <div class="alert alert-danger">
        <strong><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<script>
    // Load models when make is selected
    document.addEventListener('DOMContentLoaded', function() {
        const makeSelect = document.getElementById('make_id');
        const modelSelect = document.getElementById('model_id');

        if (makeSelect && modelSelect) {
            // Function to load models for a given make ID
            function loadModels(makeId) {
                if (!makeId) {
                    modelSelect.innerHTML = '<option value="">Select Model</option>';
                    return;
                }

                // Show loading state
                modelSelect.disabled = true;
                modelSelect.innerHTML = '<option value="">Loading models...</option>';

                fetch(`/cars/ajax/models?make_id=${makeId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(models => {
                        modelSelect.innerHTML = '<option value="">Select Model</option>';
                        if (models && models.length > 0) {
                            models.forEach(model => {
                                const option = document.createElement('option');
                                option.value = model.id;
                                option.textContent = model.name;
                                modelSelect.appendChild(option);
                            });
                        } else {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No models available';
                            modelSelect.appendChild(option);
                        }
                        modelSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading models:', error);
                        modelSelect.innerHTML = '<option value="">Error loading models</option>';
                        modelSelect.disabled = false;
                    });
            }

            // Load models when make changes
            makeSelect.addEventListener('change', function() {
                loadModels(this.value);
            });

            // Load models on page load if make is already selected (from validation errors)
            const selectedMakeId = makeSelect.value;
            if (selectedMakeId) {
                loadModels(selectedMakeId);
            }
        }
    });
</script>

<script src="{{ asset('js/car-form-validation.js') }}"></script>
