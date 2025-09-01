<?php

/**
 * Practical Examples for Using model_has_permissions and model_has_roles Tables
 * 
 * This file demonstrates various ways to utilize the Spatie Laravel Permission
 * system with direct permission assignments and role-based permissions.
 */

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

/**
 * Example 1: Creating a user with mixed permissions (role-based + direct)
 */
function createUserWithMixedPermissions()
{
    // Create a new user
    $user = User::create([
        'name' => 'John Editor',
        'email' => 'john@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now()
    ]);
    
    // Assign a base role (this adds entries to model_has_roles table)
    $user->assignRole('editor');
    
    // Add direct permissions (this adds entries to model_has_permissions table)
    $user->givePermissionTo([
        'delete articles',    // Extra permission not in editor role
        'manage comments',    // Special permission for this user
        'view analytics'      // Temporary access
    ]);
    
    echo "User created with:";
    echo "\n- Role: " . $user->roles->pluck('name')->implode(', ');
    echo "\n- Direct permissions: " . $user->permissions->pluck('name')->implode(', ');
    echo "\n- All permissions: " . $user->getAllPermissions()->pluck('name')->implode(', ');
    
    return $user;
}

/**
 * Example 2: Temporary permission assignment
 */
function giveTemporaryAdminAccess($userId, $hours = 24)
{
    $user = User::find($userId);
    
    // Give temporary admin permissions directly
    $user->givePermissionTo([
        'manage users',
        'manage roles',
        'system settings'
    ]);
    
    // You could also store expiration time in a separate table
    // and create a scheduled job to revoke permissions
    
    echo "Temporary admin access granted to {$user->name}";
    echo "\nExpires in {$hours} hours";
    
    // Example of how to revoke later
    // $user->revokePermissionTo(['manage users', 'manage roles', 'system settings']);
}

/**
 * Example 3: Permission inheritance and override
 */
function demonstratePermissionInheritance()
{
    $user = User::find(1);
    
    echo "\n=== Permission Inheritance Example ===";
    echo "\nUser: {$user->name}";
    
    // Show role-based permissions
    $rolePermissions = $user->getPermissionsViaRoles();
    echo "\nPermissions from roles: " . $rolePermissions->pluck('name')->implode(', ');
    
    // Show direct permissions
    $directPermissions = $user->permissions;
    echo "\nDirect permissions: " . $directPermissions->pluck('name')->implode(', ');
    
    // Show all permissions (combined)
    $allPermissions = $user->getAllPermissions();
    echo "\nAll permissions: " . $allPermissions->pluck('name')->implode(', ');
    
    // Check specific permission
    $canManageUsers = $user->can('manage users');
    echo "\nCan manage users: " . ($canManageUsers ? 'Yes' : 'No');
}

/**
 * Example 4: Database queries on permission tables
 */
function analyzePermissionUsage()
{
    echo "\n=== Permission Usage Analysis ===";
    
    // Count users with direct permissions
    $directPermissionCount = DB::table('model_has_permissions')
        ->where('model_type', 'App\\Models\\User')
        ->distinct('model_id')
        ->count();
    echo "\nUsers with direct permissions: {$directPermissionCount}";
    
    // Count users with roles
    $roleAssignmentCount = DB::table('model_has_roles')
        ->where('model_type', 'App\\Models\\User')
        ->distinct('model_id')
        ->count();
    echo "\nUsers with roles: {$roleAssignmentCount}";
    
    // Most assigned direct permission
    $popularPermission = DB::table('model_has_permissions')
        ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
        ->select('permissions.name', DB::raw('count(*) as total'))
        ->where('model_type', 'App\\Models\\User')
        ->groupBy('permissions.name')
        ->orderBy('total', 'desc')
        ->first();
    
    if ($popularPermission) {
        echo "\nMost assigned direct permission: {$popularPermission->name} ({$popularPermission->total} users)";
    }
    
    // Most assigned role
    $popularRole = DB::table('model_has_roles')
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->select('roles.name', DB::raw('count(*) as total'))
        ->where('model_type', 'App\\Models\\User')
        ->groupBy('roles.name')
        ->orderBy('total', 'desc')
        ->first();
    
    if ($popularRole) {
        echo "\nMost assigned role: {$popularRole->name} ({$popularRole->total} users)";
    }
}

/**
 * Example 5: Bulk permission operations
 */
function bulkPermissionOperations()
{
    echo "\n=== Bulk Permission Operations ===";
    
    // Get all users with 'editor' role
    $editors = User::role('editor')->get();
    
    foreach ($editors as $editor) {
        // Give all editors a new direct permission
        $editor->givePermissionTo('export reports');
        echo "\nGranted 'export reports' to {$editor->name}";
    }
    
    // Later, revoke the permission from all editors
    foreach ($editors as $editor) {
        $editor->revokePermissionTo('export reports');
        echo "\nRevoked 'export reports' from {$editor->name}";
    }
}

/**
 * Example 6: Permission audit trail
 */
function auditUserPermissions($userId)
{
    $user = User::with(['roles.permissions', 'permissions'])->find($userId);
    
    echo "\n=== Permission Audit for {$user->name} ===";
    
    // Show roles and their permissions
    foreach ($user->roles as $role) {
        echo "\n\nRole: {$role->name}";
        echo "\nPermissions from this role:";
        foreach ($role->permissions as $permission) {
            echo "\n  - {$permission->name}";
        }
    }
    
    // Show direct permissions
    echo "\n\nDirect Permissions (not from roles):";
    foreach ($user->permissions as $permission) {
        echo "\n  - {$permission->name}";
    }
    
    // Show database entries
    echo "\n\nDatabase Entries:";
    
    $roleEntries = DB::table('model_has_roles')
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->where('model_id', $userId)
        ->where('model_type', 'App\\Models\\User')
        ->select('roles.name')
        ->get();
    
    echo "\nmodel_has_roles entries:";
    foreach ($roleEntries as $entry) {
        echo "\n  - {$entry->name}";
    }
    
    $permissionEntries = DB::table('model_has_permissions')
        ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
        ->where('model_id', $userId)
        ->where('model_type', 'App\\Models\\User')
        ->select('permissions.name')
        ->get();
    
    echo "\nmodel_has_permissions entries:";
    foreach ($permissionEntries as $entry) {
        echo "\n  - {$entry->name}";
    }
}

/**
 * Example 7: Clean up permissions
 */
function cleanupUserPermissions($userId)
{
    $user = User::find($userId);
    
    echo "\n=== Cleaning up permissions for {$user->name} ===";
    
    // Remove all direct permissions (keeps role-based permissions)
    $directPermissions = $user->permissions->pluck('name')->toArray();
    if (!empty($directPermissions)) {
        $user->permissions()->detach();
        echo "\nRemoved direct permissions: " . implode(', ', $directPermissions);
    }
    
    // Remove all roles (and their permissions)
    $roles = $user->roles->pluck('name')->toArray();
    if (!empty($roles)) {
        $user->roles()->detach();
        echo "\nRemoved roles: " . implode(', ', $roles);
    }
    
    echo "\nUser now has no permissions or roles.";
}

/**
 * Example 8: Permission comparison between users
 */
function compareUserPermissions($userId1, $userId2)
{
    $user1 = User::find($userId1);
    $user2 = User::find($userId2);
    
    $permissions1 = $user1->getAllPermissions()->pluck('name')->toArray();
    $permissions2 = $user2->getAllPermissions()->pluck('name')->toArray();
    
    echo "\n=== Permission Comparison ===";
    echo "\n{$user1->name} permissions: " . implode(', ', $permissions1);
    echo "\n{$user2->name} permissions: " . implode(', ', $permissions2);
    
    $common = array_intersect($permissions1, $permissions2);
    $unique1 = array_diff($permissions1, $permissions2);
    $unique2 = array_diff($permissions2, $permissions1);
    
    echo "\n\nCommon permissions: " . implode(', ', $common);
    echo "\nUnique to {$user1->name}: " . implode(', ', $unique1);
    echo "\nUnique to {$user2->name}: " . implode(', ', $unique2);
}

// Usage examples (uncomment to run):
// createUserWithMixedPermissions();
// giveTemporaryAdminAccess(1, 48);
// demonstratePermissionInheritance();
// analyzePermissionUsage();
// auditUserPermissions(1);
// compareUserPermissions(1, 2);