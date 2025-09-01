# Role and Permission System Usage Guide

## Overview
This Laravel application now includes a complete role and permission management system using Spatie Laravel Permission package with CoreUI interface.

## Default Users Created
The system has been seeded with the following test users:

- **Super Admin**: `superadmin@example.com` / `password`
- **Admin**: `admin@example.com` / `password`
- **Manager**: `manager@example.com` / `password`
- **User**: `user@example.com` / `password`

## Available Routes

### Web Routes (Protected)
- `/admin/roles` - Role management interface (requires 'manage roles' permission)
- `/admin/permissions` - Permission management interface (requires 'manage permissions' permission)
- `/admin/users` - User management interface (requires 'manage users' permission)

### API Endpoints
- `GET /api/user/info` - Get current user information
- `GET /api/user/permissions` - Get user roles and permissions
- `POST /api/assign-role` - Assign role to user
- `POST /api/remove-role` - Remove role from user
- `POST /api/roles/create` - Create new role
- `POST /api/permissions/create` - Create new permission

## Using FeatureAccess Helper

```php
// Check if user has permission
if (FeatureAccess::hasPermission('manage users')) {
    // User can manage users
}

// Check if user has role
if (FeatureAccess::hasRole('admin')) {
    // User is an admin
}

// Check if user is super admin
if (FeatureAccess::isSuperAdmin()) {
    // User has super admin privileges
}

// Get user roles
$roles = FeatureAccess::getUserRoles();

// Get user permissions
$permissions = FeatureAccess::getUserPermissions();
```

## Using Middleware

```php
// Protect route with permission
Route::get('/admin/dashboard', [Controller::class, 'index'])
    ->middleware('permission:manage dashboard');

// Protect route with role
Route::get('/admin/settings', [Controller::class, 'settings'])
    ->middleware('role:admin');
```

## Using Laravel Gates

```php
// In your controller or view
if (Gate::allows('manage-users')) {
    // User can manage users
}

// In Blade templates
@can('manage-users')
    <button>Manage Users</button>
@endcan
```

## Default Roles and Permissions

### Roles:
- **super-admin**: Full system access
- **admin**: Administrative access
- **manager**: Management level access
- **editor**: Content editing access
- **user**: Basic user access

### Permissions:
- manage users
- manage roles
- manage permissions
- view dashboard
- edit content
- delete content
- view reports
- manage settings

## Testing the System

1. Login with any of the seeded users
2. Visit `/admin/roles` to manage roles
3. Visit `/admin/users` to manage user roles
4. Use the API endpoints to programmatically manage permissions

## CoreUI Interface Features

- Modern, responsive design
- Modal dialogs for creating roles/permissions
- Dynamic role assignment
- Real-time permission checking
- User-friendly error handling