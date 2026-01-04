<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <title>Dashboard - {{ config('app.name', 'CarMarketplace') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --sidebar-width: 200px;
            --sidebar-bg: {{ auth()->check() && auth()->user()->isAdmin() ? auth()->user()->getAdminColors()['sidebar_bg'] : '#23282d' }};
            --sidebar-hover: {{ auth()->check() && auth()->user()->isAdmin() ? auth()->user()->getAdminColors()['sidebar_hover'] : '#32373c' }};
            --sidebar-text: {{ auth()->check() && auth()->user()->isAdmin() ? auth()->user()->getAdminColors()['sidebar_text'] : '#b4b9be' }};
            --sidebar-active: {{ auth()->check() && auth()->user()->isAdmin() ? auth()->user()->getAdminColors()['sidebar_active'] : '#0073aa' }};
            --content-bg: {{ auth()->check() && auth()->user()->isAdmin() ? auth()->user()->getAdminColors()['content_bg'] : '#f0f0f1' }};
            --primary-color: {{ auth()->check() && auth()->user()->isAdmin() ? auth()->user()->getAdminColors()['primary_color'] : '#2271b1' }};
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--content-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .dashboard-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            background: var(--sidebar-bg);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            color: #fff;
            font-size: 20px;
            margin: 0;
            font-weight: 600;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 14px;
        }

        .sidebar-menu a:hover {
            background: var(--sidebar-hover);
            color: var(--primary-color);
        }

        .sidebar-menu a.active {
            background: var(--sidebar-active);
            color: #fff;
            border-left: 4px solid var(--primary-color);
        }

        .sidebar-menu a i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
        }

        /* Content Area */
        .dashboard-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 20px;
        }

        .dashboard-header {
            background: #fff;
            padding: 20px;
            border-bottom: 1px solid #ddd;
            margin: -20px -20px 20px -20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .dashboard-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 400;
            color: #23282d;
        }

        .dashboard-card {
            background: #fff;
            padding: 20px;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
            margin-bottom: 20px;
        }

        .dashboard-card h2 {
            font-size: 18px;
            margin: 0 0 15px 0;
            font-weight: 400;
            color: #23282d;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #fff;
            padding: 20px;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
        }

        .stat-card h3 {
            margin: 0;
            font-size: 14px;
            font-weight: 400;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 600;
            color: #23282d;
            margin: 10px 0;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .status-sold {
            background: #d1ecf1;
            color: #0c5460;
        }

        /* Table Styles */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f9f9f9;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        .table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .table tr:hover {
            background: #f9f9f9;
        }

        /* Button Styles */
        .btn {
            padding: 6px 12px;
            border-radius: 3px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            border: 1px solid;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--sidebar-active);
            border-color: var(--sidebar-active);
            color: #fff;
        }

        .btn-danger {
            background: #dc3232;
            border-color: #dc3232;
            color: #fff;
        }

        .btn-danger:hover {
            background: #b52727;
            border-color: #b52727;
            color: #fff;
        }

        .btn-secondary {
            background: #f6f7f7;
            border-color: #dcdcde;
            color: #2c3338;
        }

        .btn-secondary:hover {
            background: #f0f0f1;
            border-color: #8c8f94;
            color: #2c3338;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #23282d;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 1px var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .dashboard-sidebar.open {
                transform: translateX(0);
            }

            .dashboard-content {
                margin-left: 0;
            }
        }

        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        @auth
        <aside class="dashboard-sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-car"></i> Dashboard</h2>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.*') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                
                @if(auth()->user()->isDealer() || auth()->user()->dealer || auth()->user()->isAdmin())
                <li>
                    <a href="{{ route('dashboard.my-cars') }}" class="{{ request()->routeIs('dashboard.my-cars') ? 'active' : '' }}">
                        <i class="fas fa-car-side"></i> My Cars
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.cars.create') }}" class="{{ request()->routeIs('dashboard.cars.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> Add New Car
                    </a>
                </li>
                @elseif(auth()->user()->dealer)
                <!-- Buyer who has posted cars (has dealer profile) -->
                <li>
                    <a href="{{ route('dashboard.my-cars') }}" class="{{ request()->routeIs('dashboard.my-cars') ? 'active' : '' }}">
                        <i class="fas fa-car-side"></i> My Cars
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.cars.create') }}" class="{{ request()->routeIs('dashboard.cars.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> Post Another Car
                    </a>
                </li>
                @endif
                
                @if(auth()->user()->isBuyer() || (!auth()->user()->isDealer() && !auth()->user()->isAdmin() && !auth()->user()->dealer))
                <li>
                    <a href="{{ route('cars.index') }}">
                        <i class="fas fa-search"></i> Browse Cars
                    </a>
                </li>
                <li>
                    <a href="{{ route('favorites.index') }}">
                        <i class="fas fa-heart"></i> My Favorites
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.cars.create') }}" class="{{ request()->routeIs('dashboard.cars.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> Post Your Car
                    </a>
                </li>
                @endif

                <li>
                    <a href="{{ route('dashboard.profile') }}" class="{{ request()->routeIs('dashboard.profile') ? 'active' : '' }}">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                </li>

                @if(auth()->user()->isAdmin())
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt"></i> Admin Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.cars') }}" class="{{ request()->routeIs('admin.cars*') ? 'active' : '' }}">
                        <i class="fas fa-car"></i> Manage Cars
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.makes') }}" class="{{ request()->routeIs('admin.makes*') ? 'active' : '' }}">
                        <i class="fas fa-industry"></i> Manage Makes
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.models') }}" class="{{ request()->routeIs('admin.models*') ? 'active' : '' }}">
                        <i class="fas fa-car-side"></i> Manage Models
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.color-settings') }}" class="{{ request()->routeIs('admin.color-settings*') ? 'active' : '' }}">
                        <i class="fas fa-palette"></i> Color Settings
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.features') }}" class="{{ request()->routeIs('admin.features*') ? 'active' : '' }}">
                        <i class="fas fa-star"></i> Manage Features
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.safety-features') }}" class="{{ request()->routeIs('admin.safety-features*') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt"></i> Manage Safety Features
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.cities') }}" class="{{ request()->routeIs('admin.cities*') ? 'active' : '' }}">
                        <i class="fas fa-map-marker-alt"></i> Manage Cities
                    </a>
                </li>
                @if(auth()->user()->isSuperAdmin())
                <li style="border-top: 1px solid #32373c; margin-top: 10px; padding-top: 10px;">
                    <a href="{{ route('super-admin.admins') }}" class="{{ request()->routeIs('super-admin.*') ? 'active' : '' }}" style="color: #ffd700; font-weight: bold;">
                        <i class="fas fa-crown"></i> Super Admin
                    </a>
                </li>
                <li>
                    <a href="{{ route('super-admin.admins') }}" class="{{ request()->routeIs('super-admin.admins*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i> Manage Admins
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ url('/y') }}" target="_blank">
                        <i class="fas fa-cog"></i> Filament Panel
                    </a>
                </li>
                @endif

                <li>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: var(--sidebar-text); width: 100%; text-align: left; padding: 12px 20px; cursor: pointer; display: flex; align-items: center; font-size: 14px;">
                            <i class="fas fa-sign-out-alt" style="width: 20px; margin-right: 10px; text-align: center;"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </aside>
        @endauth

        <!-- Content Area -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <h1>
                    @if(isset($section))
                        @if($section === 'my-cars') My Cars
                        @elseif($section === 'create-car') Add New Car
                        @elseif($section === 'edit-car') Edit Car
                        @elseif($section === 'admin-dashboard') Admin Dashboard
                        @elseif($section === 'admin-users') Manage Users
                        @elseif($section === 'admin-user-create') Create User
                        @elseif($section === 'admin-user-edit') Edit User
                        @elseif($section === 'admin-cars') Manage Cars
                        @elseif($section === 'admin-makes') Manage Makes
                        @elseif($section === 'admin-make-create') Create Make
                        @elseif($section === 'admin-make-edit') Edit Make
                        @elseif($section === 'admin-models') Manage Models
                        @elseif($section === 'admin-model-create') Create Model
                        @elseif($section === 'admin-model-edit') Edit Model
                        @elseif($section === 'admin-color-settings') Color Settings
                        @elseif($section === 'admin-features') Manage Features
                        @elseif($section === 'admin-feature-create') Create Feature
                        @elseif($section === 'admin-feature-edit') Edit Feature
                        @elseif($section === 'admin-safety-features') Manage Safety Features
                        @elseif($section === 'admin-safety-feature-create') Create Safety Feature
                        @elseif($section === 'admin-safety-feature-edit') Edit Safety Feature
                        @elseif($section === 'admin-cities') Manage Cities
                        @elseif($section === 'admin-city-create') Create City
                        @elseif($section === 'admin-city-edit') Edit City
                        @elseif($section === 'super-admin-admins') Manage Admins
                        @elseif($section === 'super-admin-admin-create') Create Admin
                        @elseif($section === 'super-admin-admin-edit') Edit Admin
                        @else Dashboard
                        @endif
                    @else
                        Dashboard
                    @endif
                </h1>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Section Content -->
            @if(isset($section))
                @if($section === 'overview')
                    @include('dashboard.sections.overview')
                @elseif($section === 'my-cars')
                    @include('dashboard.sections.my-cars')
                @elseif($section === 'create-car')
                    @include('dashboard.sections.create-car')
                @elseif($section === 'edit-car')
                    @include('dashboard.sections.edit-car')
                @elseif($section === 'profile')
                    @include('dashboard.sections.profile')
                @elseif($section === 'edit-profile')
                    @include('dashboard.sections.edit-profile')
                @elseif($section === 'admin-dashboard')
                    @include('admin.dashboard')
                @elseif($section === 'admin-users')
                    @include('admin.users')
                @elseif($section === 'admin-user-create')
                    @include('admin.user-create')
                @elseif($section === 'admin-user-edit')
                    @include('admin.user-edit')
                @elseif($section === 'admin-cars')
                    @include('admin.cars')
                @elseif($section === 'admin-makes')
                    @include('admin.makes')
                @elseif($section === 'admin-make-create')
                    @include('admin.make-create')
                @elseif($section === 'admin-make-edit')
                    @include('admin.make-edit')
                @elseif($section === 'admin-models')
                    @include('admin.models')
                @elseif($section === 'admin-model-create')
                    @include('admin.model-create')
                @elseif($section === 'admin-model-edit')
                    @include('admin.model-edit')
                @elseif($section === 'admin-color-settings')
                    @include('admin.color-settings')
                @elseif($section === 'admin-features')
                    @include('admin.features')
                @elseif($section === 'admin-feature-create')
                    @include('admin.feature-create')
                @elseif($section === 'admin-feature-edit')
                    @include('admin.feature-edit')
                @elseif($section === 'admin-safety-features')
                    @include('admin.safety-features')
                @elseif($section === 'admin-safety-feature-create')
                    @include('admin.safety-feature-create')
                @elseif($section === 'admin-safety-feature-edit')
                    @include('admin.safety-feature-edit')
                @elseif($section === 'admin-cities')
                    @include('admin.cities')
                @elseif($section === 'admin-city-create')
                    @include('admin.city-create')
                @elseif($section === 'admin-city-edit')
                    @include('admin.city-edit')
                @elseif($section === 'super-admin-admins')
                    @include('super-admin.admins')
                @elseif($section === 'super-admin-admin-create')
                    @include('super-admin.admin-create')
                @elseif($section === 'super-admin-admin-edit')
                    @include('super-admin.admin-edit')
                @endif
            @else
                @include('dashboard.sections.overview')
            @endif
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load models when make is selected
        document.addEventListener('DOMContentLoaded', function() {
            const makeSelect = document.getElementById('make_id');
            const modelSelect = document.getElementById('model_id');

            if (makeSelect && modelSelect) {
                makeSelect.addEventListener('change', function() {
                    const makeId = this.value;
                    if (makeId) {
                        fetch(`/cars/ajax/models?make_id=${makeId}`)
                            .then(response => response.json())
                            .then(models => {
                                modelSelect.innerHTML = '<option value="">Select Model</option>';
                                models.forEach(model => {
                                    const option = document.createElement('option');
                                    option.value = model.id;
                                    option.textContent = model.name;
                                    modelSelect.appendChild(option);
                                });
                            });
                    } else {
                        modelSelect.innerHTML = '<option value="">Select Model</option>';
                    }
                });

                // Set initial model if editing
                @if(isset($car) && $car->model_id)
                    const initialMakeId = {{ $car->make_id }};
                    const initialModelId = {{ $car->model_id }};
                    if (makeSelect.value == initialMakeId) {
                        fetch(`/cars/ajax/models?make_id=${initialMakeId}`)
                            .then(response => response.json())
                            .then(models => {
                                models.forEach(model => {
                                    const option = document.createElement('option');
                                    option.value = model.id;
                                    option.textContent = model.name;
                                    if (model.id == initialModelId) {
                                        option.selected = true;
                                    }
                                    modelSelect.appendChild(option);
                                });
                            });
                    }
                @endif
            }
        });
    </script>
</body>
</html>
