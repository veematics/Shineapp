<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\AppSettingController;
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
    Route::get('/', [RolePermissionController::class, 'adminDashboard'])
        ->middleware('permission:manage appsetting')
        ->name('dashboard');
    
    // Role and Permission Management
    Route::get('/roles', [RolePermissionController::class, 'rolesIndex'])
        ->middleware('permission:manage roles')
        ->name('roles.index');
    
    Route::get('/permissions', [RolePermissionController::class, 'permissionsIndex'])
        ->middleware('permission:manage appsetting')
        ->name('permissions.index');
    
    Route::get('/users', [RolePermissionController::class, 'usersIndex'])
        ->middleware('permission:manage users')
        ->name('users.index');
    
    Route::get('/models', [RolePermissionController::class, 'modelsIndex'])
        ->middleware('permission:manage appsetting')
        ->name('models.index');
    
    // App Settings Routes
    Route::get('/appsetting', [AppSettingController::class, 'edit'])
        ->middleware('permission:manage appsetting')
        ->name('appsetting.edit');
    
    Route::put('/appsetting', [AppSettingController::class, 'update'])
        ->middleware('permission:manage appsetting')
        ->name('appsetting.update');
    
    Route::post('/appsetting/fetch-models', [AppSettingController::class, 'fetchModels'])
        ->middleware('permission:manage appsetting')
        ->name('appsetting.fetch-models');
    
    // Model permission management routes
    Route::post('/model-permissions', [RolePermissionController::class, 'createModelPermission'])
        ->middleware('hierarchical_permission:create permissions')
        ->name('model-permissions.create');
    
    Route::post('/model-permissions/assign-to-role', [RolePermissionController::class, 'assignModelPermissionToRole'])
        ->middleware('hierarchical_permission:edit permissions')
        ->name('model-permissions.assign-to-role');
    
    Route::delete('/model-permissions/revoke-from-role', [RolePermissionController::class, 'revokeModelPermissionFromRole'])
        ->middleware('hierarchical_permission:edit permissions')
        ->name('model-permissions.revoke-from-role');
    
    Route::get('/model-permissions/{model}', [RolePermissionController::class, 'getModelPermissions'])
        ->middleware('hierarchical_permission:view permissions')
        ->name('model-permissions.get');
    
    Route::post('/bulk-assign-permissions', [RolePermissionController::class, 'bulkAssignPermissionsToRole'])
        ->middleware('hierarchical_permission:edit permissions')
        ->name('bulk-assign-permissions');
});

// Super Admin Routes (requires super-admin role)
// Route::middleware(['auth', 'role:super-admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
//     Route::get('/', function () {
//         return view('super-admin.dashboard');
//     })->name('dashboard');
    
//     Route::get('/system-settings', function () {
//         return view('super-admin.system-settings');
//     })->name('system-settings');
// });

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
        ->middleware('hierarchical_permission:create permissions')
        ->name('permissions.create');
    
    // Direct permission assignment (bypassing roles)
    Route::post('/user/{user}/assign-direct-permission', [RolePermissionController::class, 'assignDirectPermission'])
        ->middleware('permission:manage users')
        ->name('user.assign-direct-permission');
    
    Route::delete('/user/{user}/revoke-direct-permission', [RolePermissionController::class, 'revokeDirectPermission'])
        ->middleware('permission:manage users')
        ->name('user.revoke-direct-permission');
    
    // Debug route to check user permissions
    Route::get('/debug/permissions', function() {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        return response()->json([
            'user' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'has_manage_permissions' => $user->can('manage permissions'),
            'csrf_token' => csrf_token()
        ]);
    })->middleware('auth');
    
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
        ->middleware('hierarchical_permission:view permissions')
        ->name('permissions.get');
    
    Route::put('/permissions/{id}', [RolePermissionController::class, 'updatePermission'])
        ->middleware('hierarchical_permission:edit permissions')
        ->name('permissions.update');
        
    // Permission-Role assignment routes
    Route::post('/permissions/{id}/assign-role', [RolePermissionController::class, 'assignPermissionToRole'])
        ->middleware('hierarchical_permission:edit permissions')
        ->name('permissions.assign-role');
        
    Route::post('/permissions/{id}/revoke-role', [RolePermissionController::class, 'revokePermissionFromRole'])
        ->middleware('hierarchical_permission:edit permissions')
        ->name('permissions.revoke-role');
        
    // Permission delete route
    Route::delete('/permissions/{id}', [RolePermissionController::class, 'deletePermission'])
        ->middleware('hierarchical_permission:delete permissions')
        ->name('permissions.delete');
    
    // AI Service Routes
    Route::prefix('ai')->name('ai.')->middleware('permission:manage appsetting')->group(function () {
        Route::get('/services', [AIController::class, 'services'])->name('services');
        Route::post('/chat', [AIController::class, 'chat'])->name('chat');
        Route::post('/test-openai', [AIController::class, 'testOpenAI'])->name('test-openai');
        Route::post('/test-deepseek', [AIController::class, 'testDeepSeek'])->name('test-deepseek');
    });
    
    // Google Drive Routes
    Route::prefix('googledrive')->name('googledrive.')->middleware('permission:manage appsetting')->group(function () {
        Route::get('/', [GoogleDriveController::class, 'index'])->name('index');
        Route::get('/authenticate', [GoogleDriveController::class, 'authenticate'])->name('authenticate');
        Route::get('/callback', [GoogleDriveController::class, 'callback'])->name('callback');
        Route::post('/disconnect', [GoogleDriveController::class, 'disconnect'])->name('disconnect');
        
        // API Routes
        Route::get('/auth-status', [GoogleDriveController::class, 'getAuthStatus'])->name('auth-status');
        Route::get('/files', [GoogleDriveController::class, 'listFiles'])->name('files.list');
        Route::post('/files/upload', [GoogleDriveController::class, 'uploadFile'])->name('files.upload');
        Route::get('/files/{fileId}/download', [GoogleDriveController::class, 'downloadFile'])->name('files.download');
        Route::delete('/files/{fileId}', [GoogleDriveController::class, 'deleteFile'])->name('files.delete');
        Route::get('/files/{fileId}/info', [GoogleDriveController::class, 'getFileInfo'])->name('files.info');
        Route::get('/search', [GoogleDriveController::class, 'searchFiles'])->name('search');
        Route::post('/folders', [GoogleDriveController::class, 'createFolder'])->name('folders.create');
    });
});

require __DIR__.'/auth.php';
