<?php

/**
 * Test routes for hierarchical permission system
 * 
 * This file demonstrates how the hierarchical permission system works:
 * - Users with 'manage X' permission automatically get 'create X', 'view X', 'edit X', 'delete X'
 * - Users with specific permissions (e.g., 'create X') only get that specific permission
 * 
 * To test:
 * 1. Create users with different permission combinations
 * 2. Visit these test routes to see permission behavior
 * 3. Check the responses to verify hierarchical access
 */

use Illuminate\Support\Facades\Route;
use App\Helpers\FeatureAccess;

// Test routes - add these to your web.php for testing
Route::middleware(['auth'])->prefix('test-permissions')->group(function () {
    
    // Test hierarchical permission checking
    Route::get('/test-create-permissions', function () {
        $results = [];
        
        // Test if user has create permissions permission
        $results['has_create_permissions'] = FeatureAccess::hasPermissionHierarchical('create permissions');
        
        // Test if user has manage permissions (should grant create permissions)
        $results['has_manage_permissions'] = FeatureAccess::hasPermission('manage permissions');
        
        // Show user's actual permissions
        $results['user_permissions'] = auth()->user()->getAllPermissions()->pluck('name')->toArray();
        
        // Show user's roles
        $results['user_roles'] = auth()->user()->getRoleNames()->toArray();
        
        return response()->json([
            'message' => 'Hierarchical Permission Test Results',
            'explanation' => [
                'If user has "manage permissions", they should automatically get "create permissions"',
                'If user only has "create permissions", they should NOT get "edit permissions" or "delete permissions"'
            ],
            'results' => $results
        ]);
    });
    
    Route::get('/test-edit-permissions', function () {
        $results = [];
        
        // Test if user has edit permissions permission
        $results['has_edit_permissions'] = FeatureAccess::hasPermissionHierarchical('edit permissions');
        
        // Test if user has manage permissions (should grant edit permissions)
        $results['has_manage_permissions'] = FeatureAccess::hasPermission('manage permissions');
        
        // Show user's actual permissions
        $results['user_permissions'] = auth()->user()->getAllPermissions()->pluck('name')->toArray();
        
        return response()->json([
            'message' => 'Edit Permissions Test Results',
            'results' => $results
        ]);
    });
    
    Route::get('/test-view-permissions', function () {
        $results = [];
        
        // Test if user has view permissions permission
        $results['has_view_permissions'] = FeatureAccess::hasPermissionHierarchical('view permissions');
        
        // Test if user has manage permissions (should grant view permissions)
        $results['has_manage_permissions'] = FeatureAccess::hasPermission('manage permissions');
        
        // Show user's actual permissions
        $results['user_permissions'] = auth()->user()->getAllPermissions()->pluck('name')->toArray();
        
        return response()->json([
            'message' => 'View Permissions Test Results',
            'results' => $results
        ]);
    });
    
    Route::get('/test-delete-permissions', function () {
        $results = [];
        
        // Test if user has delete permissions permission
        $results['has_delete_permissions'] = FeatureAccess::hasPermissionHierarchical('delete permissions');
        
        // Test if user has manage permissions (should grant delete permissions)
        $results['has_manage_permissions'] = FeatureAccess::hasPermission('manage permissions');
        
        // Show user's actual permissions
        $results['user_permissions'] = auth()->user()->getAllPermissions()->pluck('name')->toArray();
        
        return response()->json([
            'message' => 'Delete Permissions Test Results',
            'results' => $results
        ]);
    });
    
    // Test all permissions at once
    Route::get('/test-all-hierarchical', function () {
        $permissions_to_test = [
            'create permissions',
            'view permissions', 
            'edit permissions',
            'delete permissions',
            'manage permissions'
        ];
        
        $results = [];
        
        foreach ($permissions_to_test as $permission) {
            $results[$permission] = [
                'hierarchical_check' => FeatureAccess::hasPermissionHierarchical($permission),
                'direct_check' => FeatureAccess::hasPermission($permission)
            ];
        }
        
        return response()->json([
            'message' => 'Complete Hierarchical Permission Test',
            'explanation' => [
                'hierarchical_check: Uses new hierarchical system (manage X grants all CRUD for X)',
                'direct_check: Uses old direct permission checking'
            ],
            'user_info' => [
                'permissions' => auth()->user()->getAllPermissions()->pluck('name')->toArray(),
                'roles' => auth()->user()->getRoleNames()->toArray()
            ],
            'results' => $results
        ]);
    });
});