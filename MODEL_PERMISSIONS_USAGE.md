# Model Permissions and Roles Usage Guide

## Overview
The `model_has_permissions` and `model_has_roles` tables are pivot tables created by Spatie Laravel Permission package that establish many-to-many relationships between your models (typically Users) and permissions/roles.

## Table Structure

### model_has_permissions Table
- `permission_id` - Foreign key to permissions table
- `model_type` - The model class (e.g., 'App\Models\User')
- `model_id` - The model instance ID (e.g., user ID)

### model_has_roles Table
- `role_id` - Foreign key to roles table
- `model_type` - The model class (e.g., 'App\Models\User')
- `model_id` - The model instance ID (e.g., user ID)

## Direct Permission Assignment to Users

### Assign Permission Directly to User
```php
// Get a user
$user = User::find(1);

// Assign permission directly to user (bypassing roles)
$user->givePermissionTo('edit articles');
$user->givePermissionTo(['edit articles', 'delete articles']);

// Using permission object
$permission = Permission::findByName('edit articles');
$user->givePermissionTo($permission);
```

### Remove Direct Permission from User
```php
$user = User::find(1);

// Remove specific permission
$user->revokePermissionTo('edit articles');
$user->revokePermissionTo(['edit articles', 'delete articles']);

// Remove all direct permissions (keeps role-based permissions)
$user->permissions()->detach();
```

### Check Direct Permissions
```php
$user = User::find(1);

// Check if user has direct permission (not through roles)
$hasDirectPermission = $user->permissions->contains('name', 'edit articles');

// Get all direct permissions (not through roles)
$directPermissions = $user->permissions; // Collection of Permission models

// Get all permissions (direct + through roles)
$allPermissions = $user->getAllPermissions();
```

## Role Assignment to Users

### Assign Roles to Users
```php
$user = User::find(1);

// Assign single role
$user->assignRole('admin');

// Assign multiple roles
$user->assignRole(['admin', 'editor']);

// Using role object
$role = Role::findByName('admin');
$user->assignRole($role);
```

### Remove Roles from Users
```php
$user = User::find(1);

// Remove specific role
$user->removeRole('admin');
$user->removeRole(['admin', 'editor']);

// Remove all roles
$user->roles()->detach();
```

## Advanced Usage Examples

### 1. User with Mixed Permissions (Role-based + Direct)
```php
$user = User::find(1);

// Assign role (gives role-based permissions)
$user->assignRole('editor'); // Gets: view articles, edit articles

// Add direct permission (specific to this user)
$user->givePermissionTo('delete articles'); // Direct permission

// Now user has:
// - Role-based: view articles, edit articles (from 'editor' role)
// - Direct: delete articles
```

### 2. Temporary Permission Assignment
```php
// Give user temporary admin access
$user = User::find(1);
$user->givePermissionTo('manage users'); // Direct permission

// Later, remove temporary access
$user->revokePermissionTo('manage users');
// User keeps their role-based permissions
```

### 3. Permission Override
```php
// User has 'editor' role but needs extra permission
$user = User::find(1);
$user->assignRole('editor');
$user->givePermissionTo('publish articles'); // Extra permission not in editor role
```

## Database Queries

### Query Users with Specific Direct Permissions
```php
// Users who have 'edit articles' permission directly assigned
$users = User::whereHas('permissions', function($query) {
    $query->where('name', 'edit articles');
})->get();

// Users with specific role
$admins = User::whereHas('roles', function($query) {
    $query->where('name', 'admin');
})->get();
```

### Get Permission/Role Statistics
```php
// Count users with direct permissions
$directPermissionCount = DB::table('model_has_permissions')
    ->where('model_type', 'App\\Models\\User')
    ->count();

// Count users with roles
$roleAssignmentCount = DB::table('model_has_roles')
    ->where('model_type', 'App\\Models\\User')
    ->count();

// Most assigned permission
$popularPermission = DB::table('model_has_permissions')
    ->select('permission_id', DB::raw('count(*) as total'))
    ->where('model_type', 'App\\Models\\User')
    ->groupBy('permission_id')
    ->orderBy('total', 'desc')
    ->first();
```

## API Endpoints for Direct Permission Management

### Add to RolePermissionController
```php
/**
 * Assign permission directly to user
 */
public function assignDirectPermission(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'permission' => 'required|exists:permissions,name'
    ]);

    $user = User::findOrFail($request->user_id);
    $user->givePermissionTo($request->permission);

    return response()->json(['success' => 'Permission assigned directly to user']);
}

/**
 * Remove direct permission from user
 */
public function revokeDirectPermission(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'permission' => 'required|exists:permissions,name'
    ]);

    $user = User::findOrFail($request->user_id);
    $user->revokePermissionTo($request->permission);

    return response()->json(['success' => 'Direct permission revoked from user']);
}

/**
 * Get user's direct permissions (not through roles)
 */
public function getUserDirectPermissions($userId)
{
    $user = User::with('permissions')->findOrFail($userId);
    
    return response()->json([
        'user' => $user->name,
        'direct_permissions' => $user->permissions->pluck('name'),
        'role_permissions' => $user->getPermissionsViaRoles()->pluck('name'),
        'all_permissions' => $user->getAllPermissions()->pluck('name')
    ]);
}
```

## Best Practices

### 1. When to Use Direct Permissions
- **Temporary access**: Give user temporary elevated permissions
- **Exceptions**: User needs one extra permission not in their role
- **Fine-grained control**: Specific permissions for specific users

### 2. When to Use Roles
- **Standard access patterns**: Most users fit into predefined roles
- **Easier management**: Manage permissions at role level
- **Scalability**: Better for large user bases

### 3. Hybrid Approach (Recommended)
```php
// Standard approach: Assign roles for base permissions
$user->assignRole('editor'); // Base permissions

// Add direct permissions for exceptions
$user->givePermissionTo('manage comments'); // Extra permission
```

## Checking Permissions in Code

### In Controllers
```php
public function editArticle($id)
{
    // Checks both role-based and direct permissions
    if (!auth()->user()->can('edit articles')) {
        abort(403);
    }
    
    // Your logic here
}
```

### In Blade Templates
```blade
{{-- Shows button if user has permission (any source) --}}
@can('edit articles')
    <button>Edit Article</button>
@endcan

{{-- Check specific role --}}
@role('admin')
    <button>Admin Panel</button>
@endrole
```

### Using FeatureAccess Helper
```php
// Check permission (includes both direct and role-based)
if (FeatureAccess::hasPermission('edit articles')) {
    // User can edit articles
}

// Check role
if (FeatureAccess::hasRole('admin')) {
    // User is admin
}
```

## Migration Example for Custom Permission Assignment

```php
// In a migration or seeder
public function run()
{
    // Create special user with mixed permissions
    $user = User::create([
        'name' => 'Special Editor',
        'email' => 'special@example.com',
        'password' => bcrypt('password')
    ]);
    
    // Assign base role
    $user->assignRole('editor');
    
    // Add specific direct permissions
    $user->givePermissionTo([
        'delete articles',
        'manage comments',
        'view analytics'
    ]);
}
```

This system gives you maximum flexibility in managing user permissions through both role-based and direct permission assignment.