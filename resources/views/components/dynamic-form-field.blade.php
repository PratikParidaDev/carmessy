@props(['field', 'value' => null, 'makes' => null, 'cities' => null, 'car' => null])

@php
    $fieldName = $field['name'];
    $fieldType = $field['type'];
    $fieldLabel = $field['label'];
    $isRequired = $field['required'] ?? false;
    $isNullable = $field['nullable'] ?? false;
    $oldValue = old($fieldName, $value);
@endphp

<div class="form-group">
    <label for="{{ $fieldName }}">
        {{ $fieldLabel }}
        @if($isRequired)
            <span style="color: red;">*</span>
        @endif
    </label>

    @if($fieldType === 'select')
        <select class="form-control" 
                id="{{ $fieldName }}" 
                name="{{ $fieldName }}" 
                {{ $isRequired ? 'required' : '' }}>
            <option value="">Select {{ $fieldLabel }}</option>
            @if($fieldName === 'make_id' && $makes)
                @foreach($makes as $make)
                    <option value="{{ $make->id }}" {{ $oldValue == $make->id ? 'selected' : '' }}>
                        {{ $make->name }}
                    </option>
                @endforeach
            @elseif($fieldName === 'model_id')
                <option value="">Select Model</option>
                @if($car && $car->model_id)
                    <option value="{{ $car->model_id }}" selected>{{ $car->model->name ?? '' }}</option>
                @endif
            @elseif($fieldName === 'city_id' && $cities)
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ $oldValue == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach
            @elseif(isset($field['options']))
                @foreach($field['options'] as $option)
                    @php
                        // Default condition to 'used' for new cars
                        $isSelected = false;
                        if ($fieldName === 'condition' && is_null($oldValue)) {
                            $isSelected = ($option === 'used');
                        } else {
                            $isSelected = ($oldValue == $option);
                        }
                    @endphp
                    <option value="{{ $option }}" {{ $isSelected ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('-', ' ', $option)) }}
                    </option>
                @endforeach
            @endif
        </select>

    @elseif($fieldType === 'multi-select')
        <div style="border: 1px solid #8c8f94; border-radius: 4px; padding: 8px; max-height: 150px; overflow-y: auto;">
            @php
                $selectedValues = is_array($oldValue) ? $oldValue : (is_string($oldValue) ? json_decode($oldValue, true) : []);
            @endphp
            @if(isset($field['options']))
                @foreach($field['options'] as $option)
                    <label style="display: block; margin-bottom: 5px; font-weight: normal;">
                        <input type="checkbox" 
                               name="{{ $fieldName }}[]" 
                               value="{{ $option }}"
                               {{ in_array($option, $selectedValues ?? []) ? 'checked' : '' }}>
                        {{ $option }}
                    </label>
                @endforeach
            @endif
        </div>

    @elseif($fieldType === 'textarea')
        @if($fieldName === 'description')
            {{-- CKEditor for description field --}}
            <textarea class="form-control ckeditor-description" 
                      id="ckeditor-{{ $fieldName }}" 
                      name="{{ $fieldName }}" 
                      rows="{{ $field['rows'] ?? 6 }}"
                      {{ $isRequired ? 'required' : '' }}>{{ $oldValue }}</textarea>
        @else
            <textarea class="form-control" 
                      id="{{ $fieldName }}" 
                      name="{{ $fieldName }}" 
                      rows="{{ $field['rows'] ?? 4 }}"
                      {{ $isRequired ? 'required' : '' }}>{{ $oldValue }}</textarea>
        @endif

    @elseif($fieldType === 'boolean')
        <label style="display: flex; align-items: center; font-weight: normal; cursor: pointer;">
            <input type="checkbox" 
                   id="{{ $fieldName }}" 
                   name="{{ $fieldName }}" 
                   value="1"
                   {{ ($oldValue || ($oldValue === '1') || ($oldValue === true) || ($oldValue === 1)) ? 'checked' : '' }}
                   style="margin-right: 8px; width: auto;">
            <span>Yes</span>
        </label>
        <input type="hidden" name="{{ $fieldName }}" value="0">

    @elseif($fieldType === 'date')
        <input type="date" 
               class="form-control" 
               id="{{ $fieldName }}" 
               name="{{ $fieldName }}" 
               value="{{ $oldValue ? (is_string($oldValue) ? $oldValue : $oldValue->format('Y-m-d')) : '' }}"
               {{ $isRequired ? 'required' : '' }}>

    @elseif($fieldType === 'number')
        <input type="number" 
               class="form-control" 
               id="{{ $fieldName }}" 
               name="{{ $fieldName }}" 
               value="{{ $oldValue }}"
               @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
               @if(isset($field['max'])) max="{{ $field['max'] }}" @endif
               @if(isset($field['step'])) step="{{ $field['step'] }}" @endif
               {{ $isRequired ? 'required' : '' }}>

    @elseif($fieldType === 'file')
        {{-- Regular file upload for all file types including images --}}
        <input type="file" 
               class="form-control" 
               id="{{ $fieldName }}" 
               name="{{ $fieldName }}[]" 
               multiple 
               accept="{{ $field['accept'] ?? 'image/*' }}"
               {{ $isRequired ? 'required' : '' }}>
        <small style="color: #666; display: block; margin-top: 5px;">
            Max size: 5MB per file. You can upload multiple images.
        </small>
        @if($car && $fieldName === 'images')
            {{-- Show existing images if editing --}}
            @php
                $existingMedia = $car->getMedia('images');
            @endphp
            @if($existingMedia->count() > 0)
                <div style="margin-top: 15px;">
                    <p style="font-weight: 600; margin-bottom: 10px;">Existing Images:</p>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;" id="existing-images-container-{{ $fieldName }}">
                        @foreach($existingMedia as $media)
                        <div class="existing-image-item" data-media-id="{{ $media->id }}" style="position: relative; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; background: #fff;">
                            @php
                                $imageUrl = asset('storage/' . $media->id . '/' . $media->file_name);
                            @endphp
                            <img src="{{ $imageUrl }}" alt="Car Image" style="width: 100%; height: auto; display: block;" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'150\'%3E%3Crect width=\'150\' height=\'150\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'14\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                            <input type="hidden" name="existing_images[]" value="{{ $media->id }}" class="existing-image-input">
                            <button type="button" class="remove-image-btn" data-media-id="{{ $media->id }}" style="position: absolute; top: 5px; right: 5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); transition: all 0.2s;" title="Remove Image" onmouseover="this.style.background='#b52727'; this.style.transform='scale(1.1)'" onmouseout="this.style.background='#dc3232'; this.style.transform='scale(1)'">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="delete_images" id="delete-images-{{ $fieldName }}" value="">
                    <p style="margin-top: 10px; font-size: 13px; color: #666;">
                        <small>Click the <i class="fas fa-times" style="color: #dc3232;"></i> button to remove images. New images will be added when you upload them.</small>
                    </p>
                </div>
            @endif
        @endif

    @else
        <input type="text" 
               class="form-control" 
               id="{{ $fieldName }}" 
               name="{{ $fieldName }}" 
               value="{{ $oldValue }}"
               {{ $isRequired ? 'required' : '' }}>
    @endif

    @error($fieldName)
        <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
    @enderror
</div>

@if($car && $fieldName === 'images')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldName = '{{ $fieldName }}';
    const container = document.getElementById('existing-images-container-' + fieldName);
    const deleteInput = document.getElementById('delete-images-' + fieldName);
    
    if (!container || !deleteInput) return;
    
    let deletedMediaIds = [];
    
    // Handle remove button clicks
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-image-btn')) {
            const button = e.target.closest('.remove-image-btn');
            const mediaId = button.getAttribute('data-media-id');
            const imageItem = button.closest('.existing-image-item');
            
            if (mediaId && imageItem) {
                // Add to deleted list
                if (!deletedMediaIds.includes(mediaId)) {
                    deletedMediaIds.push(mediaId);
                    deleteInput.value = deletedMediaIds.join(',');
                }
                
                // Remove the hidden input for existing_images
                const existingInput = imageItem.querySelector('.existing-image-input');
                if (existingInput) {
                    existingInput.remove();
                }
                
                // Add fade-out animation
                imageItem.style.opacity = '0.5';
                imageItem.style.transition = 'opacity 0.3s';
                
                setTimeout(function() {
                    imageItem.style.display = 'none';
                }, 300);
            }
        }
    });
});
</script>
@endif

@if($fieldName === 'description')
@once
@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
@endpush
@endonce

@push('scripts')
<script>
(function() {
    function initCKEditor() {
        const editorId = 'ckeditor-description';
        const textarea = document.getElementById(editorId);
        
        if (!textarea) {
            // Retry if textarea not found yet
            setTimeout(initCKEditor, 100);
            return;
        }
        
        // Check if already initialized
        if (textarea.dataset.ckeditorInitialized === 'true') {
            return;
        }
        
        // Wait for CKEditor to load
        if (typeof ClassicEditor === 'undefined') {
            setTimeout(initCKEditor, 100);
            return;
        }
        
        ClassicEditor
            .create(textarea, {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'bulletedList', 'numberedList', '|',
                        'blockQuote', 'link', '|',
                        'undo', 'redo'
                    ]
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                },
                    placeholder: 'Enter car description...',
                    removePlugins: ['EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'ImageResize', 'CKFinder', 'CKFinderUploadAdapter']
            })
            .then(editor => {
                textarea.dataset.ckeditorInitialized = 'true';
                window.ckEditorInstance = editor;
                
                // Update textarea value before form submission
                const form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        textarea.value = editor.getData();
                    }, { once: true });
                }
            })
            .catch(error => {
                console.error('Error initializing CKEditor:', error);
            });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initCKEditor, 200);
        });
    } else {
        setTimeout(initCKEditor, 200);
    }
})();
</script>
@endpush
@endif

