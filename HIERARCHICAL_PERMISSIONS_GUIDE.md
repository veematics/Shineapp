# Hierarchical Permission System Guide

## Overview

This Laravel application now implements a **hierarchical permission system** where users with 'manage' permissions automatically inherit all CRUD (Create, Read, Update, Delete) operations for that resource.

## How It Works

### Permission Hierarchy

When a user has a `manage X` permission, they automatically get:
- `create X` permission
- `view X` permission  
- `edit X` permission
- `delete X` permission

### Example

If a user has `manage permissions` permission, they can:
- ✅ Create new permissions (`create permissions`)
- ✅ View existing permissions (`view permissions`)
- ✅ Edit existing permissions (`edit permissions`) 
- ✅ Delete permissions (`delete permissions`)

If a user only has `create permissions`, they can:
- ✅ Create new permissions (`create permissions`)
- ❌ View existing permissions (`view permissions`)
- ❌ Edit existing permissions (`edit permissions`)
- ❌ Delete permissions (`delete permissions`)

## Implementation Details

### 1. FeatureAccess Helper Method

A new method `hasPermissionHierarchical()` was added to `app/Helpers/FeatureAccess.php`:

```php
public static function hasPermissionHierarchical(string $permission): bool
{
    if (!Auth::check()) {
        return false;
    }

    $user = Auth::user();
    
    // First check if user has the exact permission
    if ($user->hasPermissionTo($permission)) {
        return true;
    }
    
    // Check for hierarchical permissions
    // Extract the action and resource from permission name
    $parts = explode(' ', $permission, 2);
    if (count($parts) === 2) {
        $action = $parts[0]; // create, view, edit, delete
        $resource = $parts[1]; // permissions, roles, users, etc.
        
        // If user has 'manage {resource}', they have all CRUD permissions for that resource
        if (in_array($action, ['create', 'view', 'edit', 'delete'])) {
            return $user->hasPermissionTo("manage {$resource}");
        }
    }
    
    return false;
}
```

### 2. Custom Middleware

A new middleware `HierarchicalPermission` was created at `app/Http/Middleware/HierarchicalPermission.php`:

```php
public function handle(Request $request, Closure $next, string $permission): Response
{
    if (!FeatureAccess::hasPermissionHierarchical($permission)) {
        abort(403, 'You do not have permission to access this resource.');
    }

    return $next($request);
}
```

### 3. Updated Gates

Gate definitions in `AppServiceProvider.php` now use hierarchical checking:

```php
Gate::define('manage-permissions', function ($user) {
    return \App\Helpers\FeatureAccess::hasPermissionHierarchical('manage permissions') || $user->hasRole(['admin', 'super-admin']);
});
```

### 4. Route Middleware Updates

Routes now use the new hierarchical permission middleware:

```php
// Old way
Route::post('/permissions', [RolePermissionController::class, 'createPermission'])
    ->middleware('permission:manage permissions');

// New hierarchical way
Route::post('/permissions', [RolePermissionController::class, 'createPermission'])
    ->middleware('hierarchical_permission:create permissions');
```

### 5. Controller Updates

Controller methods now use hierarchical permission checking:

```php
// Old way
if (!FeatureAccess::hasPermission('manage permissions')) {
    abort(403);
}

// New hierarchical way
if (!FeatureAccess::hasPermissionHierarchical('create permissions')) {
    abort(403);
}
```

## Usage Examples

### In Controllers

```php
use App\Helpers\FeatureAccess;

public function createPost()
{
    // Check if user can create posts (includes users with 'manage posts')
    if (!FeatureAccess::hasPermissionHierarchical('create posts')) {
        abort(403, 'You cannot create posts.');
    }
    
    // Your logic here
}

public function editPost($id)
{
    // Check if user can edit posts (includes users with 'manage posts')
    if (!FeatureAccess::hasPermissionHierarchical('edit posts')) {
        abort(403, 'You cannot edit posts.');
    }
    
    // Your logic here
}
```

### In Routes

```php
// Protect routes with hierarchical permissions
Route::middleware(['hierarchical_permission:create posts'])->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
});

Route::middleware(['hierarchical_permission:edit posts'])->group(function () {
    Route::put('/posts/{id}', [PostController::class, 'update']);
});

Route::middleware(['hierarchical_permission:delete posts'])->group(function () {
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
});
```

### In Blade Templates

```blade
{{-- Check hierarchical permissions in views --}}
@if(\App\Helpers\FeatureAccess::hasPermissionHierarchical('create posts'))
    <a href="{{ route('posts.create') }}" class="btn btn-primary">Create Post</a>
@endif

@if(\App\Helpers\FeatureAccess::hasPermissionHierarchical('edit posts'))
    <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning">Edit</a>
@endif

@if(\App\Helpers\FeatureAccess::hasPermissionHierarchical('delete posts'))
    <form method="POST" action="{{ route('posts.destroy', $post->id) }}" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
@endif
```

## Testing the System

### Test Routes

Test routes are available at `/test-permissions/*` (when logged in):

- `/test-permissions/test-create-permissions`
- `/test-permissions/test-edit-permissions` 
- `/test-permissions/test-view-permissions`
- `/test-permissions/test-delete-permissions`
- `/test-permissions/test-all-hierarchical`

### Sample Test Scenarios

1. **User with 'manage permissions'**:
   - Should have access to create, view, edit, and delete permissions
   - All hierarchical tests should return `true`

2. **User with only 'create permissions'**:
   - Should only have access to create permissions
   - Only create test should return `true`, others `false`

3. **User with 'admin' or 'super-admin' role**:
   - Should have access to everything (role-based access)
   - All tests should return `true`

## Migration Guide

### For Existing Applications

1. **Add the FeatureAccess method**: Copy the `hasPermissionHierarchical()` method to your `FeatureAccess` helper

2. **Create the middleware**: Create `HierarchicalPermission` middleware

3. **Register middleware**: Add to `bootstrap/app.php`

4. **Update routes**: Replace `permission:manage X` with `hierarchical_permission:create X` (or appropriate CRUD action)

5. **Update controllers**: Replace `hasPermission('manage X')` with `hasPermissionHierarchical('create X')` (or appropriate action)

6. **Update gates**: Modify gate definitions to use hierarchical checking

### Backward Compatibility

The old permission system still works alongside the new hierarchical system:
- Existing `FeatureAccess::hasPermission()` calls continue to work
- Existing `permission:` middleware continues to work
- Existing gate definitions continue to work

## Benefits

1. **More Granular Control**: Users can have specific CRUD permissions without full management access
2. **Intuitive Permission Structure**: 'manage X' logically includes all CRUD operations for X
3. **Easier Permission Assignment**: Assign 'manage X' for full access, or specific actions for limited access
4. **Backward Compatible**: Existing permission system continues to work
5. **Flexible**: Can be applied to any resource (posts, users, products, etc.)

## Best Practices

1. **Use hierarchical permissions for new features**: Always use `hasPermissionHierarchical()` for new code
2. **Consistent naming**: Use format `{action} {resource}` (e.g., 'create posts', 'edit users')
3. **CRUD action must be first word**: The first word MUST be a CRUD action (`create`, `view`, `edit`, `delete`)
4. **Document permissions**: Clearly document what permissions are available for each resource
5. **Test thoroughly**: Use the test routes to verify permission behavior
6. **Role-based fallback**: Keep admin/super-admin role checks as fallback in gates

### Permission Naming Rules

**✅ Correct Format**: `{CRUD_ACTION} {RESOURCE_NAME}`
- `create blog posts` → checks for `manage blog posts`
- `edit user profiles` → checks for `manage user profiles`
- `delete system settings` → checks for `manage system settings`
- `view financial reports` → checks for `manage financial reports`

**❌ Incorrect Format**: `{RESOURCE} {ACTION}` or other patterns
- `blog create posts` → No hierarchical check (first word is not CRUD action)
- `user edit profile` → No hierarchical check (first word is not CRUD action)
- `system delete files` → No hierarchical check (first word is not CRUD action)

**Important**: The hierarchical system only works when the permission starts with a CRUD action. If the first word is not `create`, `view`, `edit`, or `delete`, the system will only check for the exact permission name.

## Troubleshooting

### Common Issues

1. **Permission not working**: Check permission name format (`{action} {resource}`)
2. **User still can't access**: Verify user has the permission or appropriate role
3. **Middleware not working**: Ensure middleware is registered in `bootstrap/app.php`
4. **Gate not working**: Check gate definition uses `hasPermissionHierarchical()`

### Debug Steps

1. Check user permissions: `auth()->user()->getAllPermissions()`
2. Check user roles: `auth()->user()->getRoleNames()`
3. Test hierarchical method: `FeatureAccess::hasPermissionHierarchical('permission name')`
4. Use test routes to verify behavior

## Future Enhancements

Possible future improvements:
1. **Multi-level hierarchy**: Support for more complex permission hierarchies
2. **Resource-specific permissions**: Permissions tied to specific resource instances
3. **Time-based permissions**: Temporary permission grants
4. **Permission inheritance**: Child resources inheriting parent permissions