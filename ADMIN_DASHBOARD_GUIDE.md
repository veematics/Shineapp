# Admin Dashboard Guide

## Overview
The admin dashboard provides a comprehensive 2-column layout for managing your Laravel application with Spatie Laravel-Permission integration.

## Layout Structure

### Left Column: Admin Menu
- **Location**: `resources/views/layouts/partials/admin-menu.blade.php`
- **Purpose**: Navigation sidebar with admin settings and management options
- **Features**:
  - User Management (Users, Roles, Permissions)
  - Content Management (Posts, Categories, Media)
  - System Settings (General, Email, Security)
  - Reports & Analytics
  - System Tools (Database, Logs, Cache)

### Right Column: Content Area
- **Purpose**: Main content display area
- **Features**:
  - Dynamic page titles
  - User welcome message
  - Logout functionality
  - Responsive design

## Accessing the Admin Dashboard

### URL
```
http://localhost:5173/admin
```

### Requirements
1. **Authentication**: User must be logged in
2. **Authorization**: User must have `admin-access` permission
3. **Roles**: Typically assigned to users with `admin` or `super-admin` roles

### Sample Users (from seeder)
- **Super Admin**: `admin@example.com` (password: `admin`)
- **Admin**: `admin@admin.com` (password: `password`)

## File Structure

```
resources/views/
├── layouts/
│   ├── admin.blade.php              # Main admin layout
│   └── partials/
│       └── admin-menu.blade.php     # Admin sidebar menu
└── admin/
    └── dashboard.blade.php          # Admin dashboard content
```

## Features

### Dashboard Content
- **Welcome Message**: "Hello World" placeholder
- **Quick Stats**: User count, roles count, permissions count, system status
- **Recent Activity**: Placeholder for activity logs
- **Quick Actions**: Direct links to user, role, and permission management

### Responsive Design
- **Desktop**: 2-column layout with fixed sidebar
- **Mobile**: Stacked layout with collapsible sidebar

### Navigation Highlights
- **Active State**: Current page highlighted in sidebar
- **Hover Effects**: Smooth transitions and visual feedback
- **Icons**: FontAwesome icons for better UX
- **Grouping**: Logical grouping of related functions

## Customization

### Adding New Menu Items
Edit `resources/views/layouts/partials/admin-menu.blade.php`:

```html
<li class="mb-1">
    <a href="{{ route('admin.your-route') }}" class="d-flex align-items-center text-decoration-none text-white p-3 ps-4 hover-bg-primary">
        <i class="fas fa-your-icon me-3"></i>
        <span>Your Menu Item</span>
    </a>
</li>
```

### Modifying Dashboard Content
Edit `resources/views/admin/dashboard.blade.php` to replace the "Hello World" placeholder with your actual dashboard widgets.

### Styling
- **CSS Framework**: Bootstrap 5 + CoreUI Pro
- **Custom Styles**: Included in layout files
- **Color Scheme**: Professional dark sidebar with light content area

## Security

### Permission Checks
- **Gate**: `admin-access` gate defined in `AppServiceProvider`
- **Middleware**: `can:admin-access` middleware on routes
- **Controller**: Additional permission checks in controller methods

### Route Protection
```php
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [RolePermissionController::class, 'adminDashboard'])
        ->middleware('can:admin-access')
        ->name('dashboard');
});
```

## Development Notes

### Adding New Admin Pages
1. Create view in `resources/views/admin/`
2. Add route in `routes/web.php` under admin group
3. Add menu item in admin-menu partial
4. Implement controller method if needed

### Permission Integration
All admin functions integrate with Spatie Laravel-Permission:
- Role-based access control
- Permission-based feature access
- User role management
- Dynamic permission checking

## Troubleshooting

### Access Denied (403)
- Ensure user has `admin-access` permission
- Check if user has appropriate role (admin/super-admin)
- Verify gate definition in `AppServiceProvider`

### Layout Issues
- Check CoreUI CSS is loaded
- Verify Bootstrap 5 compatibility
- Ensure responsive meta tags are present

### Menu Not Working
- Verify route names match menu links
- Check if routes are properly defined
- Ensure middleware is correctly applied

## Next Steps

1. **Replace Placeholder**: Replace "Hello World" with actual dashboard widgets
2. **Add Charts**: Integrate charts and analytics
3. **Real-time Data**: Add real-time updates for statistics
4. **Activity Logs**: Implement actual activity logging
5. **Notifications**: Add admin notification system

The admin dashboard is now ready for use and can be accessed at `/admin` by authorized users!