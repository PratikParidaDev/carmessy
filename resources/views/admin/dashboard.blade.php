<div class="dashboard-card">
    <h2>Admin Dashboard</h2>
    <p>Welcome, <strong>{{ auth()->user()->name }}</strong> (Administrator)</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Users</h3>
        <div class="stat-value">{{ $stats['total_users'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Dealers</h3>
        <div class="stat-value">{{ $stats['total_dealers'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Buyers</h3>
        <div class="stat-value">{{ $stats['total_buyers'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Total Cars</h3>
        <div class="stat-value">{{ $stats['total_cars'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Pending Cars</h3>
        <div class="stat-value" style="color: #856404;">{{ $stats['pending_cars'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Approved Cars</h3>
        <div class="stat-value" style="color: #155724;">{{ $stats['approved_cars'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Rejected Cars</h3>
        <div class="stat-value" style="color: #721c24;">{{ $stats['rejected_cars'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Sold Cars</h3>
        <div class="stat-value" style="color: #0c5460;">{{ $stats['sold_cars'] }}</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="dashboard-card">
    <h2>Quick Actions</h2>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('admin.users') }}" class="btn btn-primary">
            <i class="fas fa-users"></i> Manage Users
        </a>
        <a href="{{ route('admin.cars') }}" class="btn btn-primary">
            <i class="fas fa-car"></i> Manage Cars
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-home"></i> Regular Dashboard
        </a>
    </div>
</div>

<!-- Recent Pending Cars -->
@if($pendingCars->count() > 0)
<div class="dashboard-card">
    <h2>Recent Pending Cars</h2>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Price</th>
                    <th>Dealer</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingCars as $car)
                <tr>
                    <td>
                        @if($car->getFirstMediaUrl('images'))
                            <img src="{{ $car->getFirstMediaUrl('images') }}" alt="{{ $car->title }}" 
                                 style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                        @else
                            <div style="width: 60px; height: 40px; background: #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-car" style="color: #999;"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $car->title }}</strong>
                    </td>
                    <td>{{ $car->make->name ?? 'N/A' }}</td>
                    <td>{{ $car->model->name ?? 'N/A' }}</td>
                    <td>₹ {{ number_format($car->price, 0) }}</td>
                    <td>
                        <small>{{ $car->dealer->user->name ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $car->created_at->format('M j, Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <form action="{{ route('admin.cars.approve', $car->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary" style="padding: 4px 8px; font-size: 12px;">
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.cars.reject', $car->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px;">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top: 15px;">
        <a href="{{ route('admin.cars') }}" class="btn btn-secondary">View All Cars</a>
    </div>
</div>
@endif

<!-- Recent Users -->
@if($recentUsers->count() > 0)
<div class="dashboard-card">
    <h2>Recent Users</h2>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentUsers as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="status-badge" style="background: {{ $user->role === 'admin' ? '#2271b1' : ($user->role === 'dealer' ? '#28a745' : '#6c757d') }}; color: #fff;">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->format('M j, Y') }}</td>
                    <td>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" style="display: inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this user? This will also delete all their cars and dealer profile.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding: 4px 8px; font-size: 12px;">
                                Delete
                            </button>
                        </form>
                        @else
                        <span style="color: #999; font-size: 12px;">Current User</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top: 15px;">
        <a href="{{ route('admin.users') }}" class="btn btn-secondary">View All Users</a>
    </div>
</div>
@endif

