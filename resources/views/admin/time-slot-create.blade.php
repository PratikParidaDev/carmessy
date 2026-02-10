<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 18px;">Create New Time Slot</h2>
        <a href="{{ route('admin.time-slots') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Time Slots
        </a>
    </div>

    <form action="{{ route('admin.time-slots.store') }}" method="POST">
        @csrf
        
        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Time Slot Name <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="e.g., 09:00 AM - 11:00 AM"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">This is what users will see in the dropdown</small>
            </div>

            <div class="col-md-3">
                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                <input type="time" 
                       class="form-control @error('start_time') is-invalid @enderror" 
                       id="start_time" 
                       name="start_time" 
                       value="{{ old('start_time') }}" 
                       required>
                @error('start_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                <input type="time" 
                       class="form-control @error('end_time') is-invalid @enderror" 
                       id="end_time" 
                       name="end_time" 
                       value="{{ old('end_time') }}" 
                       required>
                @error('end_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="sort_order" class="form-label">Sort Order</label>
                <input type="number" 
                       class="form-control @error('sort_order') is-invalid @enderror" 
                       id="sort_order" 
                       name="sort_order" 
                       value="{{ old('sort_order', 0) }}" 
                       min="0">
                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Lower numbers appear first in the dropdown</small>
            </div>

            <div class="col-md-6">
                <div class="form-check mt-4">
                    <input class="form-check-input" 
                           type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active (Visible to users)
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create Time Slot
            </button>
            <a href="{{ route('admin.time-slots') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate name from start and end time
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const nameInput = document.getElementById('name');
    
    function formatTime(timeString) {
        if (!timeString) return '';
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return `${displayHour.toString().padStart(2, '0')}:${minutes} ${ampm}`;
    }
    
    function updateName() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        
        if (startTime && endTime && !nameInput.value) {
            const formattedStart = formatTime(startTime);
            const formattedEnd = formatTime(endTime);
            nameInput.value = `${formattedStart} - ${formattedEnd}`;
        }
    }
    
    startTimeInput.addEventListener('change', updateName);
    endTimeInput.addEventListener('change', updateName);
});
</script>
@endpush

