/**
 * Car Form Validation - Frontend Validation
 */
class CarFormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId) || document.querySelector('form[action*="cars"]');
        this.errors = {};
        this.init();
    }

    init() {
        if (!this.form) return;

        // Add validation on form submit
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
                this.showErrors();
                this.scrollToFirstError();
            }
        });

        // Real-time validation on blur
        this.setupRealTimeValidation();
    }

    setupRealTimeValidation() {
        const fields = this.form.querySelectorAll('input, select, textarea');
        
        fields.forEach(field => {
            // Skip file inputs (handled separately)
            if (field.type === 'file') return;

            field.addEventListener('blur', () => {
                this.validateField(field);
            });

            field.addEventListener('input', () => {
                // Clear error on input for text fields
                if (['text', 'number', 'textarea'].includes(field.type)) {
                    this.clearFieldError(field);
                }
            });
        });

        // Special handling for images
        const imageInput = this.form.querySelector('input[name="images[]"]');
        if (imageInput) {
            imageInput.addEventListener('change', () => {
                this.validateImages();
            });
        }
    }

    validateForm() {
        this.errors = {};
        let isValid = true;

        // Validate required fields
        isValid = this.validateRequired() && isValid;
        
        // Validate specific field types
        isValid = this.validateTitle() && isValid;
        isValid = this.validateYear() && isValid;
        isValid = this.validatePrice() && isValid;
        isValid = this.validateSelects() && isValid;
        isValid = this.validateNumbers() && isValid;
        isValid = this.validateTextFields() && isValid;
        isValid = this.validateImages() && isValid;
        isValid = this.validateDates() && isValid;

        return isValid;
    }

    validateRequired() {
        let isValid = true;
        const requiredFields = this.form.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            if (field.type === 'checkbox') {
                if (!field.checked) {
                    this.addError(field.name, `${this.getFieldLabel(field)} is required.`);
                    isValid = false;
                }
            } else if (field.type === 'file') {
                // Skip file inputs here, handled in validateImages
            } else {
                const value = field.value.trim();
                if (!value || value === '' || value === '0' || value === 'null') {
                    this.addError(field.name, `${this.getFieldLabel(field)} is required.`);
                    isValid = false;
                }
            }
        });

        return isValid;
    }

    validateTitle() {
        const title = this.form.querySelector('[name="title"]');
        if (!title) return true;

        const value = title.value.trim();
        if (value.length < 10) {
            this.addError('title', 'Title must be at least 10 characters long.');
            return false;
        }
        if (value.length > 255) {
            this.addError('title', 'Title cannot exceed 255 characters.');
            return false;
        }
        return true;
    }

    validateYear() {
        const year = this.form.querySelector('[name="year"]');
        if (!year || !year.value) return true;

        const yearValue = parseInt(year.value);
        const currentYear = new Date().getFullYear();
        const nextYear = currentYear + 1;

        if (isNaN(yearValue)) {
            this.addError('year', 'Year must be a valid number.');
            return false;
        }
        if (yearValue < 1900) {
            this.addError('year', 'Year must be 1900 or later.');
            return false;
        }
        if (yearValue > nextYear) {
            this.addError('year', 'Year cannot be in the future.');
            return false;
        }
        return true;
    }

    validatePrice() {
        const price = this.form.querySelector('[name="price"]');
        if (!price || !price.value) return true;

        const priceValue = parseFloat(price.value);
        if (isNaN(priceValue)) {
            this.addError('price', 'Price must be a valid number.');
            return false;
        }
        if (priceValue < 1000) {
            this.addError('price', 'Price must be at least ₹1,000.');
            return false;
        }
        if (priceValue > 100000000) {
            this.addError('price', 'Price seems too high. Please verify.');
            return false;
        }
        return true;
    }

    validateSelects() {
        let isValid = true;
        const selects = ['make_id', 'model_id', 'city_id', 'fuel_type', 'transmission', 'condition'];
        
        selects.forEach(name => {
            const select = this.form.querySelector(`[name="${name}"]`);
            if (select && select.hasAttribute('required')) {
                if (!select.value || select.value === '' || select.value === '0') {
                    this.addError(name, `${this.getFieldLabel(select)} is required.`);
                    isValid = false;
                }
            }
        });

        return isValid;
    }

    validateNumbers() {
        let isValid = true;
        const numberFields = [
            { name: 'mileage', min: 0, max: 1000000 },
            { name: 'power', min: 0, max: 2000 },
            { name: 'torque', min: 0, max: 5000 },
            { name: 'mileage_kmpl', min: 0, max: 100 },
            { name: 'seats', min: 2, max: 15, required: true },
            { name: 'doors', min: 2, max: 6, required: true },
            { name: 'owners', min: 0, max: 10, required: true },
        ];

        numberFields.forEach(field => {
            const input = this.form.querySelector(`[name="${field.name}"]`);
            if (!input) return;

            const value = input.value.trim();
            
            // Check if required
            if (field.required && (!value || value === '')) {
                this.addError(field.name, `${this.getFieldLabel(input)} is required.`);
                isValid = false;
                return;
            }

            // Skip validation if field is empty and not required
            if (!value && !field.required) return;

            const numValue = field.name.includes('kmpl') ? parseFloat(value) : parseInt(value);
            
            if (isNaN(numValue)) {
                this.addError(field.name, `${this.getFieldLabel(input)} must be a valid number.`);
                isValid = false;
            } else {
                if (field.min !== undefined && numValue < field.min) {
                    this.addError(field.name, `${this.getFieldLabel(input)} must be at least ${field.min}.`);
                    isValid = false;
                }
                if (field.max !== undefined && numValue > field.max) {
                    this.addError(field.name, `${this.getFieldLabel(input)} value seems too high. Please verify.`);
                    isValid = false;
                }
            }
        });

        return isValid;
    }

    validateTextFields() {
        let isValid = true;
        const textFields = [
            { name: 'exterior_color', required: true, max: 255 },
            { name: 'interior_color', required: false, max: 255 },
            { name: 'vin', required: false, max: 255, pattern: /^[A-HJ-NPR-Z0-9]{17}$/i },
            { name: 'description', required: false, max: 5000 },
        ];

        textFields.forEach(field => {
            const input = this.form.querySelector(`[name="${field.name}"]`);
            if (!input) return;

            const value = input.value.trim();

            if (field.required && !value) {
                this.addError(field.name, `${this.getFieldLabel(input)} is required.`);
                isValid = false;
                return;
            }

            if (value && field.max && value.length > field.max) {
                this.addError(field.name, `${this.getFieldLabel(input)} cannot exceed ${field.max} characters.`);
                isValid = false;
            }

            if (value && field.pattern && !field.pattern.test(value)) {
                if (field.name === 'vin') {
                    this.addError(field.name, 'VIN must be a valid 17-character alphanumeric code.');
                } else {
                    this.addError(field.name, `${this.getFieldLabel(input)} format is invalid.`);
                }
                isValid = false;
            }
        });

        return isValid;
    }

    validateImages() {
        const imageInput = this.form.querySelector('input[name="images[]"]');
        if (!imageInput) return true;

        const files = imageInput.files;
        const existingImages = this.form.querySelectorAll('[data-media-id]').length;
        const totalImages = files.length + existingImages;

        // Check if at least one image exists
        if (totalImages === 0) {
            this.addError('images', 'At least one image is required.');
            return false;
        }

        // Validate each file
        let isValid = true;
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

        Array.from(files).forEach((file, index) => {
            if (!allowedTypes.includes(file.type)) {
                this.addError('images', `Image ${index + 1} must be in JPEG, JPG, PNG, or WEBP format.`);
                isValid = false;
            }
            if (file.size > maxSize) {
                this.addError('images', `Image ${index + 1} must not exceed 5MB in size.`);
                isValid = false;
            }
        });

        return isValid;
    }

    validateDates() {
        const insuranceExpiry = this.form.querySelector('[name="insurance_expiry"]');
        if (!insuranceExpiry || !insuranceExpiry.value) return true;

        const expiryDate = new Date(insuranceExpiry.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (expiryDate <= today) {
            this.addError('insurance_expiry', 'Insurance expiry date must be in the future.');
            return false;
        }

        return true;
    }

    validateField(field) {
        const fieldName = field.name;
        const value = field.value.trim();

        // Clear previous error
        this.clearFieldError(field);

        // Validate based on field type
        if (field.hasAttribute('required') && (!value || value === '' || value === '0')) {
            this.addError(fieldName, `${this.getFieldLabel(field)} is required.`);
            this.showFieldError(field);
            return false;
        }

        // Specific validations
        if (fieldName === 'title' && value) {
            if (value.length < 10) {
                this.addError(fieldName, 'Title must be at least 10 characters long.');
                this.showFieldError(field);
                return false;
            }
        }

        if (fieldName === 'year' && value) {
            const yearValue = parseInt(value);
            const currentYear = new Date().getFullYear();
            if (isNaN(yearValue) || yearValue < 1900 || yearValue > currentYear + 1) {
                this.addError(fieldName, 'Please enter a valid year (1900 to ' + (currentYear + 1) + ').');
                this.showFieldError(field);
                return false;
            }
        }

        if (fieldName === 'price' && value) {
            const priceValue = parseFloat(value);
            if (isNaN(priceValue) || priceValue < 1000) {
                this.addError(fieldName, 'Price must be at least ₹1,000.');
                this.showFieldError(field);
                return false;
            }
        }

        return true;
    }

    addError(fieldName, message) {
        if (!this.errors[fieldName]) {
            this.errors[fieldName] = [];
        }
        this.errors[fieldName].push(message);
    }

    showErrors() {
        // Clear previous errors
        this.form.querySelectorAll('.validation-error').forEach(el => el.remove());
        this.form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        // Show new errors
        Object.keys(this.errors).forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"], [name="${fieldName}[]"]`);
            if (field) {
                this.showFieldError(field, this.errors[fieldName][0]);
            }
        });
    }

    showFieldError(field, message = null) {
        field.classList.add('is-invalid');
        
        const errorMessage = message || this.errors[field.name]?.[0] || 'This field has an error.';
        
        // Remove existing error message
        const existingError = field.parentElement.querySelector('.validation-error');
        if (existingError) {
            existingError.remove();
        }

        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error';
        errorDiv.style.cssText = 'color:rgb(244, 155, 100); font-size: 13px; margin-top: 5px; display: block;';
        errorDiv.textContent = errorMessage;
        
        field.parentElement.appendChild(errorDiv);
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorDiv = field.parentElement.querySelector('.validation-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    getFieldLabel(field) {
        const label = field.closest('.form-group')?.querySelector('label');
        if (label) {
            return label.textContent.replace('*', '').trim();
        }
        return field.name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    scrollToFirstError() {
        const firstError = this.form.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on a car form page
    const carForm = document.querySelector('form[action*="cars"]');
    if (carForm) {
        window.carFormValidator = new CarFormValidator();
    }
});

