<div class="dashboard-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Admin Panel Color Settings</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
        </a>
    </div>

    <p style="color: #666; margin-bottom: 30px;">
        Customize the color scheme of your admin panel. Changes will be applied immediately and saved to your account.
    </p>

    <!-- Color Presets -->
    <div class="dashboard-card" style="margin-bottom: 30px;">
        <h3 style="font-size: 16px; margin-bottom: 15px; color: #23282d; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
            <i class="fas fa-palette"></i> Color Presets
        </h3>
        <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
            Choose from predefined color schemes or create your own custom colors below.
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
            @foreach($presets as $key => $preset)
            <form action="{{ route('admin.color-settings.preset') }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="preset" value="{{ $key }}">
                <button type="submit" 
                        style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; background: #fff; cursor: pointer; text-align: left; transition: all 0.3s;"
                        onmouseover="this.style.borderColor='{{ $preset['primary_color'] }}'; this.style.transform='translateY(-2px)';"
                        onmouseout="this.style.borderColor='#ddd'; this.style.transform='translateY(0)';">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        <div style="display: flex; gap: 3px;">
                            <div style="width: 20px; height: 20px; background: {{ $preset['sidebar_bg'] }}; border-radius: 3px; border: 1px solid #ddd;"></div>
                            <div style="width: 20px; height: 20px; background: {{ $preset['sidebar_active'] }}; border-radius: 3px; border: 1px solid #ddd;"></div>
                            <div style="width: 20px; height: 20px; background: {{ $preset['primary_color'] }}; border-radius: 3px; border: 1px solid #ddd;"></div>
                        </div>
                        <strong style="font-size: 14px; color: #23282d;">{{ $preset['name'] }}</strong>
                    </div>
                    <div style="font-size: 12px; color: #666;">
                        Click to apply
                    </div>
                </button>
            </form>
            @endforeach
        </div>
    </div>

    <!-- Custom Color Picker -->
    <div class="dashboard-card">
        <h3 style="font-size: 16px; margin-bottom: 15px; color: #23282d; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
            <i class="fas fa-sliders-h"></i> Custom Colors
        </h3>
        
        <form action="{{ route('admin.color-settings.save') }}" method="POST" id="color-form">
            @csrf

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <!-- Sidebar Background -->
                <div class="form-group">
                    <label for="sidebar_bg" style="display: block; margin-bottom: 8px; font-weight: 500;">
                        Sidebar Background
                    </label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input 
                            type="color" 
                            id="sidebar_bg" 
                            name="sidebar_bg" 
                            value="{{ $currentColors['sidebar_bg'] }}"
                            style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;"
                            onchange="updatePreview()"
                        />
                        <input 
                            type="text" 
                            value="{{ $currentColors['sidebar_bg'] }}"
                            id="sidebar_bg_text"
                            style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace;"
                            onchange="document.getElementById('sidebar_bg').value = this.value; updatePreview();"
                            pattern="^#[0-9A-Fa-f]{6}$"
                        />
                    </div>
                </div>

                <!-- Sidebar Hover -->
                <div class="form-group">
                    <label for="sidebar_hover" style="display: block; margin-bottom: 8px; font-weight: 500;">
                        Sidebar Hover
                    </label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input 
                            type="color" 
                            id="sidebar_hover" 
                            name="sidebar_hover" 
                            value="{{ $currentColors['sidebar_hover'] }}"
                            style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;"
                            onchange="updatePreview()"
                        />
                        <input 
                            type="text" 
                            value="{{ $currentColors['sidebar_hover'] }}"
                            id="sidebar_hover_text"
                            style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace;"
                            onchange="document.getElementById('sidebar_hover').value = this.value; updatePreview();"
                            pattern="^#[0-9A-Fa-f]{6}$"
                        />
                    </div>
                </div>

                <!-- Sidebar Text -->
                <div class="form-group">
                    <label for="sidebar_text" style="display: block; margin-bottom: 8px; font-weight: 500;">
                        Sidebar Text
                    </label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input 
                            type="color" 
                            id="sidebar_text" 
                            name="sidebar_text" 
                            value="{{ $currentColors['sidebar_text'] }}"
                            style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;"
                            onchange="updatePreview()"
                        />
                        <input 
                            type="text" 
                            value="{{ $currentColors['sidebar_text'] }}"
                            id="sidebar_text_text"
                            style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace;"
                            onchange="document.getElementById('sidebar_text').value = this.value; updatePreview();"
                            pattern="^#[0-9A-Fa-f]{6}$"
                        />
                    </div>
                </div>

                <!-- Sidebar Active -->
                <div class="form-group">
                    <label for="sidebar_active" style="display: block; margin-bottom: 8px; font-weight: 500;">
                        Sidebar Active Item
                    </label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input 
                            type="color" 
                            id="sidebar_active" 
                            name="sidebar_active" 
                            value="{{ $currentColors['sidebar_active'] }}"
                            style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;"
                            onchange="updatePreview()"
                        />
                        <input 
                            type="text" 
                            value="{{ $currentColors['sidebar_active'] }}"
                            id="sidebar_active_text"
                            style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace;"
                            onchange="document.getElementById('sidebar_active').value = this.value; updatePreview();"
                            pattern="^#[0-9A-Fa-f]{6}$"
                        />
                    </div>
                </div>

                <!-- Content Background -->
                <div class="form-group">
                    <label for="content_bg" style="display: block; margin-bottom: 8px; font-weight: 500;">
                        Content Background
                    </label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input 
                            type="color" 
                            id="content_bg" 
                            name="content_bg" 
                            value="{{ $currentColors['content_bg'] }}"
                            style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;"
                            onchange="updatePreview()"
                        />
                        <input 
                            type="text" 
                            value="{{ $currentColors['content_bg'] }}"
                            id="content_bg_text"
                            style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace;"
                            onchange="document.getElementById('content_bg').value = this.value; updatePreview();"
                            pattern="^#[0-9A-Fa-f]{6}$"
                        />
                    </div>
                </div>

                <!-- Primary Color -->
                <div class="form-group">
                    <label for="primary_color" style="display: block; margin-bottom: 8px; font-weight: 500;">
                        Primary Color (Buttons, Links)
                    </label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input 
                            type="color" 
                            id="primary_color" 
                            name="primary_color" 
                            value="{{ $currentColors['primary_color'] }}"
                            style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;"
                            onchange="updatePreview()"
                        />
                        <input 
                            type="text" 
                            value="{{ $currentColors['primary_color'] }}"
                            id="primary_color_text"
                            style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace;"
                            onchange="document.getElementById('primary_color').value = this.value; updatePreview();"
                            pattern="^#[0-9A-Fa-f]{6}$"
                        />
                    </div>
                </div>
            </div>

            <!-- Live Preview -->
            <div style="margin-top: 30px; padding: 20px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px;">
                <h4 style="margin-bottom: 15px; font-size: 16px;">Live Preview</h4>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <div id="preview-sidebar" style="background: {{ $currentColors['sidebar_bg'] }}; padding: 15px; border-radius: 4px; margin-bottom: 10px;">
                            <div style="color: {{ $currentColors['sidebar_text'] }}; font-weight: 600; margin-bottom: 10px;">Sidebar</div>
                            <div style="background: {{ $currentColors['sidebar_active'] }}; padding: 8px; border-radius: 4px; color: #fff; margin-bottom: 5px; font-size: 13px;">Active Item</div>
                            <div style="color: {{ $currentColors['sidebar_text'] }}; padding: 8px; font-size: 13px;">Menu Item</div>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <div style="background: {{ $currentColors['content_bg'] }}; padding: 15px; border-radius: 4px; margin-bottom: 10px;">
                            <div style="background: #fff; padding: 15px; border-radius: 4px; margin-bottom: 10px;">
                                <div style="font-weight: 600; margin-bottom: 10px;">Content Card</div>
                                <button style="background: {{ $currentColors['primary_color'] }}; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 13px;">
                                    Primary Button
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-size: 16px;">
                    <i class="fas fa-save"></i> Save Color Scheme
                </button>
                <form action="{{ route('admin.color-settings.reset') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" 
                            class="btn btn-secondary" 
                            style="padding: 12px 24px; font-size: 16px;"
                            onclick="return confirm('Are you sure you want to reset to default colors?');">
                        <i class="fas fa-undo"></i> Reset to Default
                    </button>
                </form>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary" style="padding: 12px 24px; font-size: 16px;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function updatePreview() {
        // Update text inputs when color picker changes
        const colorInputs = ['sidebar_bg', 'sidebar_hover', 'sidebar_text', 'sidebar_active', 'content_bg', 'primary_color'];
        
        colorInputs.forEach(id => {
            const colorPicker = document.getElementById(id);
            const textInput = document.getElementById(id + '_text');
            if (colorPicker && textInput) {
                textInput.value = colorPicker.value;
            }
        });

        // Update live preview
        const sidebarBg = document.getElementById('sidebar_bg').value;
        const sidebarText = document.getElementById('sidebar_text').value;
        const sidebarActive = document.getElementById('sidebar_active').value;
        const contentBg = document.getElementById('content_bg').value;
        const primaryColor = document.getElementById('primary_color').value;

        const previewSidebar = document.getElementById('preview-sidebar');
        if (previewSidebar) {
            previewSidebar.style.background = sidebarBg;
            previewSidebar.querySelector('div:first-child').style.color = sidebarText;
            previewSidebar.querySelector('div:nth-child(2)').style.background = sidebarActive;
            previewSidebar.querySelector('div:last-child').style.color = sidebarText;
        }

        const previewContent = document.querySelector('#color-form > div:nth-child(3) > div:last-child');
        if (previewContent) {
            previewContent.style.background = contentBg;
            const button = previewContent.querySelector('button');
            if (button) {
                button.style.background = primaryColor;
            }
        }

        // Apply colors to actual dashboard in real-time (for preview)
        if (document.documentElement) {
            document.documentElement.style.setProperty('--sidebar-bg', sidebarBg);
            document.documentElement.style.setProperty('--sidebar-hover', document.getElementById('sidebar_hover').value);
            document.documentElement.style.setProperty('--sidebar-text', sidebarText);
            document.documentElement.style.setProperty('--sidebar-active', sidebarActive);
            document.documentElement.style.setProperty('--content-bg', contentBg);
            document.documentElement.style.setProperty('--primary-color', primaryColor);
        }
    }

    // Sync text inputs with color pickers
    document.addEventListener('DOMContentLoaded', function() {
        const colorInputs = ['sidebar_bg', 'sidebar_hover', 'sidebar_text', 'sidebar_active', 'content_bg', 'primary_color'];
        
        colorInputs.forEach(id => {
            const colorPicker = document.getElementById(id);
            const textInput = document.getElementById(id + '_text');
            
            if (colorPicker && textInput) {
                // Sync on color picker change
                colorPicker.addEventListener('input', function() {
                    textInput.value = this.value;
                    updatePreview();
                });
                
                // Sync on text input change
                textInput.addEventListener('input', function() {
                    if (/^#[0-9A-Fa-f]{6}$/i.test(this.value)) {
                        colorPicker.value = this.value;
                        updatePreview();
                    }
                });
            }
        });
    });
</script>

