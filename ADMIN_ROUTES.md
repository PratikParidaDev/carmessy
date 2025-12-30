# Admin Routes - Complete List

## 🎯 Main Admin Access Points

### 1. **Filament Admin Panel** (Primary Admin Interface)
- **URL**: `/y`
- **Login**: `/y/login`
- **Logout**: `/y/logout` (POST)
- **Dashboard**: `/y` (Filament Dashboard)

### 2. **Custom Dashboard** (WordPress-style)
- **URL**: `/dashboard`
- **Access**: Available to admins with full CRUD on all cars

---

## 📋 Filament Admin Panel Routes (`/y`)

### Cars Management
- **List All Cars**: `/y/cars`
- **Create Car**: `/y/cars/create`
- **View Car**: `/y/cars/{car}`
- **Edit Car**: `/y/cars/{car}/edit`
- **Delete Car**: `/y/cars/{car}` (DELETE)

### Dealers Management
- **List All Dealers**: `/y/dealers`
- **Create Dealer**: `/y/dealers/create`
- **View Dealer**: `/y/dealers/{dealer}`
- **Edit Dealer**: `/y/dealers/{dealer}/edit`
- **Delete Dealer**: `/y/dealers/{dealer}` (DELETE)

---

## 🎛️ Custom Dashboard Routes (`/dashboard`)

### Dashboard Overview
- **GET** `/dashboard` - Main dashboard (shows all cars for admin)

### Car Management
- **GET** `/dashboard/my-cars` - View all cars (admin sees all cars)
- **GET** `/dashboard/cars/create` - Create new car
- **POST** `/dashboard/cars` - Store new car
- **GET** `/dashboard/cars/{car}/edit` - Edit car
- **PUT** `/dashboard/cars/{car}` - Update car
- **DELETE** `/dashboard/cars/{car}` - Delete car

### Profile
- **GET** `/profile` - Edit profile
- **PATCH** `/profile` - Update profile
- **DELETE** `/profile` - Delete profile

---

## 🔐 Admin Access Requirements

### Authentication
- Must be logged in (`auth` middleware)
- Must have `role = 'admin'` in users table

### Access Control
- **Filament Panel**: Accessible to any authenticated user (can be restricted)
- **Dashboard Routes**: Available to admins (full access to all cars)
- **CRUD Operations**: Admin can manage all cars from any dealer

---

## 🚀 Quick Access Links

### For Admin Users:
1. **Filament Admin Panel**: `http://your-domain.com/y`
2. **Custom Dashboard**: `http://your-domain.com/dashboard`
3. **Admin Panel Link**: Available in sidebar when logged in as admin

### Sidebar Navigation (when admin):
- Dashboard → `/dashboard`
- My Cars → `/dashboard/my-cars`
- Add New Car → `/dashboard/cars/create`
- Admin Panel → `/y` (opens in new tab)
- Profile → `/profile`

---

## 📊 Admin Capabilities

### In Filament Panel (`/y`):
- ✅ Full CRUD on Cars (all dealers' cars)
- ✅ Full CRUD on Dealers
- ✅ Approve/Reject car listings
- ✅ Manage car status (pending, approved, rejected, sold)
- ✅ Edit restricted fields (status, is_featured, is_verified, admin_notes)
- ✅ View and manage all dealers

### In Custom Dashboard (`/dashboard`):
- ✅ View all cars from all dealers
- ✅ Create cars for any dealer
- ✅ Edit any car (no status reset)
- ✅ Delete any car
- ✅ See dealer name for each car
- ✅ View stats for all cars in system

---

## 🔄 Route Comparison

| Feature | Filament Panel (`/y`) | Custom Dashboard (`/dashboard`) |
|---------|----------------------|-------------------------------|
| **Interface** | Modern Filament UI | WordPress-style UI |
| **Cars Management** | ✅ Full CRUD | ✅ Full CRUD |
| **Dealers Management** | ✅ Full CRUD | ❌ Not available |
| **Status Management** | ✅ Advanced | ✅ Basic |
| **Bulk Actions** | ✅ Built-in | ⚠️ Ready for implementation |
| **Approval Workflow** | ✅ Advanced | ✅ Basic |
| **Schema-Driven** | ❌ Hardcoded | ✅ Dynamic |

---

## 💡 Usage Recommendations

### Use Filament Panel (`/y`) for:
- Advanced car management
- Dealer management
- Approval workflows
- Bulk operations
- Detailed admin tasks

### Use Custom Dashboard (`/dashboard`) for:
- Quick car overview
- Simple CRUD operations
- WordPress-style interface
- Schema-driven forms
- User-friendly interface

---

## 🔧 Configuration

### Filament Panel Configuration
- **File**: `app/Providers/Filament/YPanelProvider.php`
- **Path**: `/y` (configured in `->path('y')`)
- **Resources**: Auto-discovered from `app/Filament/Resources`

### Dashboard Routes
- **File**: `routes/web.php`
- **Middleware**: `auth`, `verified`
- **Controller**: `DashboardController`

---

## 📝 Notes

1. **Both interfaces are available** - Admin can use either Filament or custom dashboard
2. **Same data source** - Both access the same `cars` table
3. **Permissions** - Admin has full access in both interfaces
4. **Sidebar link** - Admin sees "Admin Panel" link in custom dashboard sidebar

