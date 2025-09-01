<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Helpers\FeatureAccess;
use Illuminate\Support\Facades\Gate;

class RolePermissionController extends Controller
{
    /**
     * Display roles management page
     */
    public function rolesIndex()
    {
        // Check permission using FeatureAccess helper
        if (!FeatureAccess::hasPermission('manage roles')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        $roles = Role::with('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Display permissions management page
     */
    public function permissionsIndex()
    {
        // Check permission using Gates
        if (Gate::denies('manage-permissions')) {
            abort(403, 'You do not have permission to manage permissions.');
        }

        $permissions = Permission::with('roles')->get();
        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Display users with their roles and permissions
     */
    public function usersIndex()
    {
        if (!FeatureAccess::hasPermission('manage users')) {
            abort(403, 'You do not have permission to manage users.');
        }

        $users = User::with(['roles', 'permissions'])->get();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Display models management page
     */
    public function modelsIndex()
    {
        // Check permission using Laravel Gates
        if (!Gate::allows('manage-models')) {
            abort(403, 'Unauthorized access to model management.');
        }

        // Get registered models from configuration
        $registeredModels = config('models.registered_models', []);
        $models = [];
        
        // Process registered models
        foreach ($registeredModels as $key => $config) {
            $models[$key] = [
                'name' => $key,
                'display_name' => $config['display_name'] ?? $key,
                'description' => $config['description'] ?? '',
                'class' => $config['class'] ?? '',
                'permissions' => $this->getModelPermissionsInternal($key),
                'roles' => $this->getModelRoles($key),
                'suggested_permissions' => $config['permissions'] ?? []
            ];
        }
        
        // Auto-discover models if enabled
        if (config('models.discovery.enabled', true)) {
            $discoveredModels = $this->discoverModels();
            foreach ($discoveredModels as $modelName => $modelData) {
                if (!isset($models[$modelName])) {
                    $models[$modelName] = $modelData;
                }
            }
        }

        // Get all permissions and roles for assignment
        $allPermissions = Permission::all();
        $allRoles = Role::all();

        return view('admin.models.index', compact('models', 'allPermissions', 'allRoles'));
    }

    /**
     * Get permissions related to a specific model (for internal use)
     */
    private function getModelPermissionsInternal($modelName)
    {
        $modelLower = strtolower($modelName);
        return Permission::where('name', 'like', "%{$modelLower}%")
            ->orWhere('name', 'like', "%{$modelName}%")
            ->get();
    }

    /**
     * Get roles that have permissions related to a specific model
     */
    private function getModelRoles($modelName)
    {
        // Get roles that have permissions related to this model
        $modelPermissions = Permission::where('name', 'like', '%' . strtolower($modelName) . '%')->get();
        $roles = [];
        
        foreach ($modelPermissions as $permission) {
            $permissionRoles = $permission->roles;
            foreach ($permissionRoles as $role) {
                if (!in_array($role->name, $roles)) {
                    $roles[] = $role->name;
                }
            }
        }
        
        return $roles;
    }
    
    /**
     * Discover models from configured directories
     */
    private function discoverModels()
    {
        $models = [];
        $directories = config('models.discovery.directories', [app_path('Models')]);
        $exclude = config('models.discovery.exclude', []);
        
        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                $files = scandir($directory);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                        $modelName = pathinfo($file, PATHINFO_FILENAME);
                        
                        if (!in_array($modelName, $exclude)) {
                            $models[$modelName] = [
                                'name' => $modelName,
                                'display_name' => $modelName,
                                'description' => 'Auto-discovered model',
                                'class' => 'App\\Models\\' . $modelName,
                                'permissions' => $this->getModelPermissionsInternal($modelName),
                                'roles' => $this->getModelRoles($modelName),
                                'suggested_permissions' => $this->generateSuggestedPermissions($modelName)
                            ];
                        }
                    }
                }
            }
        }
        
        return $models;
    }
    
    /**
     * Generate suggested permissions for a model based on naming convention
     */
    private function generateSuggestedPermissions($modelName)
    {
        $pattern = config('models.permission_naming.pattern', '{action} {model_lower}');
        $actions = config('models.permission_naming.actions', ['view', 'create', 'edit', 'delete', 'manage']);
        $permissions = [];
        
        foreach ($actions as $action) {
            $permission = str_replace(
                ['{action}', '{model}', '{model_lower}'],
                [$action, $modelName, strtolower($modelName)],
                $pattern
            );
            $permissions[] = $permission;
        }
        
        return $permissions;
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request)
    {
        if (!FeatureAccess::hasPermission('manage users')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->assignRole($request->role);

        return response()->json(['success' => 'Role assigned successfully']);
    }

    /**
     * Remove role from user
     */
    public function removeRole(Request $request)
    {
        if (!FeatureAccess::hasPermission('manage users')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->removeRole($request->role);

        return response()->json(['success' => 'Role removed successfully']);
    }

    /**
     * Create new role
     */
    public function createRole(Request $request)
    {
        if (!FeatureAccess::hasPermission('create roles')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if ($request->permissions) {
            $role->givePermissionTo($request->permissions);
        }

        return response()->json(['success' => 'Role created successfully']);
    }

    /**
     * Create new permission
     */
    public function createPermission(Request $request)
    {
        if (!FeatureAccess::hasPermission('create permissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

        Permission::create(['name' => $request->name]);

        return response()->json(['success' => 'Permission created successfully']);
    }

    /**
     * Get user permissions and roles for API
     */
    public function getUserInfo()
    {
        return response()->json([
            'user' => auth()->user()->name,
            'roles' => FeatureAccess::getUserRoles()->pluck('name'),
            'permissions' => FeatureAccess::getUserPermissions()->pluck('name'),
            'is_super_admin' => FeatureAccess::isSuperAdmin(),
            'can_manage_users' => FeatureAccess::hasPermission('manage users'),
            'can_manage_roles' => FeatureAccess::hasPermission('manage roles'),
            'can_manage_permissions' => FeatureAccess::hasPermission('manage permissions'),
        ]);
    }

    /**
     * Assign permission directly to user (bypassing roles)
     */
    public function assignDirectPermission(Request $request)
    {
        if (!FeatureAccess::hasPermission('manage users')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        if (!FeatureAccess::hasPermission('manage users')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        if (!FeatureAccess::hasPermission('manage users')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::with(['permissions', 'roles.permissions'])->findOrFail($userId);
        
        return response()->json([
            'user' => $user->name,
            'direct_permissions' => $user->permissions->pluck('name'),
            'role_permissions' => $user->getPermissionsViaRoles()->pluck('name'),
            'all_permissions' => $user->getAllPermissions()->pluck('name'),
            'roles' => $user->roles->pluck('name')
        ]);
    }

    /**
     * Create a model-specific permission
     */
    public function createModelPermission(Request $request)
    {
        if (Gate::denies('manage-permissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'model' => 'required|string',
            'permission_type' => 'required|in:view,create,edit,delete,manage',
            'permission_name' => 'required|string|unique:permissions,name',
            'description' => 'nullable|string',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        // Create the permission
        $permission = Permission::create([
            'name' => $request->permission_name,
            'guard_name' => 'web'
        ]);

        // Assign to roles if specified
        if ($request->has('roles') && !empty($request->roles)) {
            $roles = Role::whereIn('id', $request->roles)->get();
            foreach ($roles as $role) {
                $role->givePermissionTo($permission);
            }
        }

        return response()->json([
            'success' => 'Model permission created successfully',
            'permission' => $permission,
            'assigned_roles' => $request->roles ?? []
        ]);
    }

    /**
     * Assign permission to role for specific model
     */
    public function assignModelPermissionToRole(Request $request)
    {
        if (Gate::denies('manage-permissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        $permission = Permission::findOrFail($request->permission_id);
        $role = Role::findOrFail($request->role_id);

        $role->givePermissionTo($permission);

        return response()->json([
            'success' => "Permission '{$permission->name}' assigned to role '{$role->name}'"
        ]);
    }

    /**
     * Revoke permission from role for specific model
     */
    public function revokeModelPermissionFromRole(Request $request)
    {
        if (Gate::denies('manage-permissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        $permission = Permission::findOrFail($request->permission_id);
        $role = Role::findOrFail($request->role_id);

        $role->revokePermissionTo($permission);

        return response()->json([
            'success' => "Permission '{$permission->name}' revoked from role '{$role->name}'"
        ]);
    }

    /**
     * Get model permissions and their role assignments
     */
    public function getModelPermissions(Request $request)
    {
        if (Gate::denies('manage-permissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'model' => 'required|string'
        ]);

        $modelName = $request->model;
        $modelLower = strtolower($modelName);
        
        $permissions = Permission::where('name', 'like', "%{$modelLower}%")
            ->orWhere('name', 'like', "%{$modelName}%")
            ->with('roles')
            ->get();

        return response()->json([
            'model' => $modelName,
            'permissions' => $permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'roles' => $permission->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name
                        ];
                    })
                ];
            })
        ]);
    }

    /**
     * Bulk assign permissions to role
     */
    public function bulkAssignPermissionsToRole(Request $request)
    {
        if (Gate::denies('manage-permissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id'
        ]);

        $role = Role::findOrFail($request->role_id);
        $permissions = Permission::whereIn('id', $request->permission_ids)->get();

        foreach ($permissions as $permission) {
            $role->givePermissionTo($permission);
        }

        return response()->json([
            'success' => count($permissions) . " permissions assigned to role '{$role->name}'"
        ]);
    }

    /**
     * Bulk revoke permissions from role
     */
    public function bulkRevokePermissionsFromRole(Request $request)
    {
        if (Gate::denies('manage-permissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id'
        ]);

        $role = Role::findOrFail($request->role_id);
        $permissions = Permission::whereIn('id', $request->permission_ids)->get();

        foreach ($permissions as $permission) {
            $role->revokePermissionTo($permission);
        }

        return response()->json([
            'success' => count($permissions) . " permissions revoked from role '{$role->name}'"
        ]);
    }

    /**
     * Update permission
     */
    public function updatePermission(Request $request, $id)
    {
        if (!FeatureAccess::hasPermission('manage permissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:permissions,name,' . $id,
                'guard_name' => 'nullable|string|in:web,api'
            ]);

            $permission = Permission::findOrFail($id);
            $permission->update([
                'name' => $request->name,
                'guard_name' => $request->guard_name ?? 'web'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'permission' => $permission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get single permission for editing
     */
    public function getPermission($id)
    {
        if (!FeatureAccess::hasPermission('manage permissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $permission = Permission::with('roles')->findOrFail($id);
            $allRoles = Role::all();
            
            return response()->json([
                'success' => true,
                'permission' => $permission,
                'allRoles' => $allRoles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Permission not found'
            ], 404);
        }
    }

    /**
     * Assign permission to role
     */
    public function assignPermissionToRole(Request $request, $permissionId)
    {
        if (!FeatureAccess::hasPermission('manage permissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $request->validate([
                'role_id' => 'required|exists:roles,id'
            ]);

            $permission = Permission::findOrFail($permissionId);
            $role = Role::findOrFail($request->role_id);
            
            $role->givePermissionTo($permission);

            return response()->json([
                'success' => true,
                'message' => 'Permission assigned to role successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Revoke permission from role
     */
    public function revokePermissionFromRole(Request $request, $permissionId)
    {
        if (!FeatureAccess::hasPermission('manage permissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $request->validate([
                'role_id' => 'required|exists:roles,id'
            ]);

            $permission = Permission::findOrFail($permissionId);
            $role = Role::findOrFail($request->role_id);
            
            $role->revokePermissionTo($permission);

            return response()->json([
                'success' => true,
                'message' => 'Permission revoked from role successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}