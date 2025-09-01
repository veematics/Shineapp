<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// User Management Routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/users', function () {
        return view('users.index');
    })->middleware('permission:view users')->name('users.index');
    
    Route::get('/settings', function () {
        return view('settings.index');
    })->middleware('permission:manage settings')->name('settings.index');
});

// Admin Routes (requires admin or super-admin role)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->middleware('can:admin-access')->name('dashboard');
    
    // Role and Permission Management
    Route::get('/roles', [RolePermissionController::class, 'rolesIndex'])
        ->middleware('can:manage-roles')
        ->name('roles.index');
    
    Route::get('/permissions', [RolePermissionController::class, 'permissionsIndex'])
        ->middleware('can:manage-permissions')
        ->name('permissions.index');
    
    Route::get('/users', [RolePermissionController::class, 'usersIndex'])
        ->middleware('can:manage-users')
        ->name('users.index');
    
    Route::get('/models', [RolePermissionController::class, 'modelsIndex'])
        ->middleware('can:manage-models')
        ->name('models.index');
    
    // Model permission management routes
    Route::post('/model-permissions', [RolePermissionController::class, 'createModelPermission'])
        ->middleware('permission:manage permissions')
        ->name('model-permissions.create');
    
    Route::post('/model-permissions/assign-to-role', [RolePermissionController::class, 'assignModelPermissionToRole'])
        ->middleware('permission:manage permissions')
        ->name('model-permissions.assign-to-role');
    
    Route::delete('/model-permissions/revoke-from-role', [RolePermissionController::class, 'revokeModelPermissionFromRole'])
        ->middleware('permission:manage permissions')
        ->name('model-permissions.revoke-from-role');
    
    Route::get('/model-permissions/{model}', [RolePermissionController::class, 'getModelPermissions'])
        ->middleware('permission:manage permissions')
        ->name('model-permissions.get');
    
    Route::post('/bulk-assign-permissions', [RolePermissionController::class, 'bulkAssignPermissionsToRole'])
        ->middleware('permission:manage permissions')
        ->name('bulk-assign-permissions');
});

// Super Admin Routes (requires super-admin role)
Route::middleware(['auth', 'role:super-admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/', function () {
        return view('super-admin.dashboard');
    })->name('dashboard');
    
    Route::get('/system-settings', function () {
        return view('super-admin.system-settings');
    })->name('system-settings');
});

// Reports Routes (requires specific permissions)
Route::middleware(['auth', 'permission:view reports'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', function () {
        return view('reports.index');
    })->name('index');
    
    Route::get('/analytics', function () {
        return view('reports.analytics');
    })->middleware('permission:view analytics')->name('analytics');
});

// API Routes for Role and Permission Management
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    // User information
    Route::get('/user/{user}', [RolePermissionController::class, 'getUserInfo'])
        ->middleware('permission:view users')
        ->name('user.info');
    
    // Role assignment
    Route::post('/user/{user}/assign-role', [RolePermissionController::class, 'assignRole'])
        ->middleware('permission:assign roles')
        ->name('user.assign-role');
    
    Route::delete('/user/{user}/remove-role/{role}', [RolePermissionController::class, 'removeRole'])
        ->middleware('permission:assign roles')
        ->name('user.remove-role');
    
    // Role and Permission creation
    Route::post('/roles', [RolePermissionController::class, 'createRole'])
        ->middleware('permission:create roles')
        ->name('roles.create');
    
    Route::post('/permissions', [RolePermissionController::class, 'createPermission'])
        ->middleware('permission:create permissions')
        ->name('permissions.create');
    
    // Direct permission assignment (bypassing roles)
    Route::post('/user/{user}/assign-direct-permission', [RolePermissionController::class, 'assignDirectPermission'])
        ->middleware('permission:manage users')
        ->name('user.assign-direct-permission');
    
    Route::delete('/user/{user}/revoke-direct-permission', [RolePermissionController::class, 'revokeDirectPermission'])
        ->middleware('permission:manage users')
        ->name('user.revoke-direct-permission');
    
    Route::get('/user/{user}/direct-permissions', [RolePermissionController::class, 'getUserDirectPermissions'])
        ->middleware('permission:manage users')
        ->name('user.direct-permissions');


    
    Route::post('/bulk-revoke-permissions', [RolePermissionController::class, 'bulkRevokePermissionsFromRole'])
        ->middleware('permission:manage permissions')
        ->name('bulk-revoke-permissions');

    // Permission checks
    Route::get('/check-permission/{permission}', function ($permission) {
        return response()->json([
            'has_permission' => request()->user()->can($permission)
        ]);
    })->name('check-permission');
    
    Route::get('/check-role/{role}', function ($role) {
        return response()->json([
            'has_role' => request()->user()->hasRole($role)
        ]);
    })->name('check-role');
    
    // Permission edit routes
    Route::get('/permissions/{id}', [RolePermissionController::class, 'getPermission'])
        ->middleware('permission:manage permissions')
        ->name('permissions.get');
    
    Route::put('/permissions/{id}', [RolePermissionController::class, 'updatePermission'])
        ->middleware('permission:manage permissions')
        ->name('permissions.update');
        
    // Permission-Role assignment routes
    Route::post('/permissions/{id}/assign-role', [RolePermissionController::class, 'assignPermissionToRole'])
        ->middleware('permission:manage permissions')
        ->name('permissions.assign-role');
        
    Route::post('/permissions/{id}/revoke-role', [RolePermissionController::class, 'revokePermissionFromRole'])
        ->middleware('permission:manage permissions')
        ->name('permissions.revoke-role');
});

require __DIR__.'/auth.php';
