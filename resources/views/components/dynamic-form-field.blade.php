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
        <textarea class="form-control" 
                  id="{{ $fieldName }}" 
                  name="{{ $fieldName }}" 
                  rows="{{ $field['rows'] ?? 4 }}"
                  {{ $isRequired ? 'required' : '' }}>{{ $oldValue }}</textarea>

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
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;">
                        @foreach($existingMedia as $media)
                        <div style="position: relative; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                            <img src="{{ $media->getUrl() }}" alt="Car Image" style="width: 100%; height: auto; display: block;">
                            <input type="hidden" name="existing_images[]" value="{{ $media->id }}">
                        </div>
                        @endforeach
                    </div>
                    <p style="margin-top: 10px; font-size: 13px; color: #666;">
                        <small>To delete existing images, uncheck them or leave them as is. New images will be added.</small>
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

