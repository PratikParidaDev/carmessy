@props(['field', 'car' => null, 'existingImages' => null])

@php
    $fieldName = $field['name'] ?? 'images';
    $fieldLabel = $field['label'] ?? 'Images';
    $isRequired = $field['required'] ?? false;
    
    // Get existing images if editing
    $existingMedia = $car ? $car->getMedia('images') : collect();
@endphp

<div class="form-group">
    <label for="{{ $fieldName }}">
        {{ $fieldLabel }}
        @if($isRequired)
            <span style="color: red;">*</span>
        @endif
    </label>

    <!-- WordPress-style Image Upload Container -->
    <div id="wp-image-upload-{{ $fieldName }}" class="wp-image-upload-container">
        <!-- Upload Area -->
        <div class="wp-upload-area" id="upload-area-{{ $fieldName }}">
            <div class="wp-upload-content">
                <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #8c8f94; margin-bottom: 15px;"></i>
                <p style="margin: 0; color: #666; font-size: 14px;">
                    <strong>Drop images here</strong> or <a href="#" class="wp-upload-link" onclick="document.getElementById('file-input-{{ $fieldName }}').click(); return false;">click to upload</a>
                </p>
                <p style="margin: 5px 0 0 0; color: #999; font-size: 12px;">
                    Maximum upload file size: 5MB. You can upload multiple images.
                </p>
            </div>
            <input type="file" 
                   id="file-input-{{ $fieldName }}" 
                   name="{{ $fieldName }}[]" 
                   multiple 
                   accept="image/*" 
                   style="display: none;"
                   {{ $isRequired && $existingMedia->count() === 0 ? 'required' : '' }}
                   onchange="handleFileSelect_{{ $fieldName }}(this)">
        </div>

        <!-- Image Gallery -->
        <div class="wp-image-gallery" id="image-gallery-{{ $fieldName }}">
            @if($existingMedia && $existingMedia->count() > 0)
                @foreach($existingMedia as $index => $media)
                <div class="wp-image-item" data-media-id="{{ $media->id }}">
                    <div class="wp-image-preview">
                        @php
                            $imageUrl = asset('storage/' . $media->id . '/' . $media->file_name);
                        @endphp
                        <img src="{{ $imageUrl }}" alt="Car Image" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'150\'%3E%3Crect width=\'150\' height=\'150\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'14\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                        <div class="wp-image-overlay">
                            <button type="button" class="wp-image-delete" data-media-id="{{ $media->id }}" title="Delete">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @if($index === 0)
                            <div class="wp-featured-badge">Featured</div>
                        @endif
                    </div>
                    <input type="hidden" name="existing_images[]" value="{{ $media->id }}">
                </div>
                @endforeach
            @endif
        </div>

        <!-- Hidden input for deleted images -->
        <input type="hidden" name="delete_images" id="delete-images-{{ $fieldName }}" value="">
    </div>

    @error($fieldName)
        <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
    @enderror
</div>

<style>
    .wp-image-upload-container {
        margin-top: 10px;
    }

    .wp-upload-area {
        border: 2px dashed #8c8f94;
        border-radius: 4px;
        padding: 40px 20px;
        text-align: center;
        background: #f9f9f9;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 15px;
    }

    .wp-upload-area:hover {
        border-color: #2271b1;
        background: #f0f6fc;
    }

    .wp-upload-area.dragover {
        border-color: #2271b1;
        background: #e8f4f8;
    }

    .wp-upload-content {
        pointer-events: none;
    }

    .wp-upload-link {
        color: #2271b1;
        text-decoration: none;
        pointer-events: auto;
    }

    .wp-upload-link:hover {
        text-decoration: underline;
    }

    .wp-image-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .wp-image-item {
        position: relative;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
        transition: all 0.2s;
    }

    .wp-image-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .wp-image-preview {
        position: relative;
        width: 100%;
        padding-top: 75%; /* 4:3 aspect ratio */
        background: #f0f0f0;
    }

    .wp-image-preview img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .wp-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .wp-image-item:hover .wp-image-overlay {
        opacity: 1;
    }

    .wp-image-delete {
        background: #dc3232;
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.2s;
    }

    .wp-image-delete:hover {
        background: #b52727;
        transform: scale(1.1);
    }

    .wp-featured-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #2271b1;
        color: white;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .wp-image-item.uploading {
        opacity: 0.6;
        pointer-events: none;
    }

    .wp-image-item.uploading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 30px;
        height: 30px;
        border: 3px solid #2271b1;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: translate(-50%, -50%) rotate(360deg); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fieldName = '{{ $fieldName }}';
        const uploadArea = document.getElementById('upload-area-' + fieldName);
        const fileInput = document.getElementById('file-input-' + fieldName);
        const imageGallery = document.getElementById('image-gallery-' + fieldName);
        const deleteInput = document.getElementById('delete-images-' + fieldName);
        
        if (!uploadArea || !fileInput || !imageGallery || !deleteInput) {
            console.error('Image upload elements not found for field:', fieldName);
            return;
        }
        
        let deletedMediaIds = [];

        // Drag and drop handlers
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        uploadArea.addEventListener('click', function() {
            fileInput.click();
        });

        // Global function for file input change
        window['handleFileSelect_' + fieldName] = function(input) {
            handleFiles(input.files);
        };

        function handleFiles(files) {
            const dataTransfer = new DataTransfer();
            
            // Add existing files from input
            Array.from(fileInput.files).forEach(file => {
                dataTransfer.items.add(file);
            });
            
            // Add new files
            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    if (file.size > 5 * 1024 * 1024) {
                        alert('File ' + file.name + ' is too large. Maximum size is 5MB.');
                        return;
                    }
                    // Check if file already exists
                    const exists = Array.from(fileInput.files).some(f => f.name === file.name && f.size === file.size);
                    if (!exists) {
                        dataTransfer.items.add(file);
                        previewImage(file);
                    }
                }
            });
            
            // Update file input
            fileInput.files = dataTransfer.files;
        }

        function previewImage(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageItem = document.createElement('div');
                imageItem.className = 'wp-image-item';
                imageItem.setAttribute('data-file-name', file.name);
                imageItem.innerHTML = `
                    <div class="wp-image-preview">
                        <img src="${e.target.result}" alt="Preview">
                        <div class="wp-image-overlay">
                            <button type="button" class="wp-image-delete" onclick="removeImageItem(this)" title="Delete">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                imageGallery.appendChild(imageItem);
            };
            reader.onerror = function() {
                console.error('Error reading file:', file.name);
                alert('Error reading file: ' + file.name);
            };
            reader.readAsDataURL(file);
        }

        // Delete image handlers
        window.removeImageItem = function(button) {
            if (!button) return;
            const imageItem = button.closest('.wp-image-item');
            if (!imageItem) return;
            
            const mediaId = imageItem.getAttribute('data-media-id');
            const fileName = imageItem.getAttribute('data-file-name');
            
            if (mediaId) {
                // Existing image - mark for deletion
                if (!deletedMediaIds.includes(mediaId)) {
                    deletedMediaIds.push(mediaId);
                    deleteInput.value = deletedMediaIds.join(',');
                }
            } else if (fileName) {
                // New file - need to remove from file input
                // Create new FileList without this file
                const dataTransfer = new DataTransfer();
                Array.from(fileInput.files).forEach(file => {
                    if (file.name !== fileName) {
                        dataTransfer.items.add(file);
                    }
                });
                fileInput.files = dataTransfer.files;
            }
            
            imageItem.remove();
        };

        // Handle delete buttons for existing images
        document.querySelectorAll('#wp-image-upload-' + fieldName + ' .wp-image-delete[data-media-id]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                removeImageItem(this);
            });
        });
    });
</script>

