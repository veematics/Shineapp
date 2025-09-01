<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FeatureAccess
{
    /**
     * Check if the current user has a specific permission
     *
     * @param string $permission
     * @return bool
     */
    public static function hasPermission(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasPermissionTo($permission);
    }

    /**
     * Check if the current user has a specific role
     *
     * @param string $role
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole($role);
    }

    /**
     * Check if the current user has any of the given roles
     *
     * @param array $roles
     * @return bool
     */
    public static function hasAnyRole(array $roles): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAnyRole($roles);
    }

    /**
     * Check if the current user has all of the given roles
     *
     * @param array $roles
     * @return bool
     */
    public static function hasAllRoles(array $roles): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAllRoles($roles);
    }

    /**
     * Check if the current user has any of the given permissions
     *
     * @param array $permissions
     * @return bool
     */
    public static function hasAnyPermission(array $permissions): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAnyPermission($permissions);
    }

    /**
     * Check if the current user has all of the given permissions
     *
     * @param array $permissions
     * @return bool
     */
    public static function hasAllPermissions(array $permissions): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAllPermissions($permissions);
    }

    /**
     * Check if the current user can access a specific feature using Laravel Gates
     *
     * @param string $gate
     * @param mixed $model
     * @return bool
     */
    public static function canAccess(string $gate, $model = null): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Gate::allows($gate, $model);
    }

    /**
     * Check if the current user cannot access a specific feature using Laravel Gates
     *
     * @param string $gate
     * @param mixed $model
     * @return bool
     */
    public static function cannotAccess(string $gate, $model = null): bool
    {
        return !self::canAccess($gate, $model);
    }

    /**
     * Get all roles for the current user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserRoles()
    {
        if (!Auth::check()) {
            return collect();
        }

        return Auth::user()->roles;
    }

    /**
     * Get all permissions for the current user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserPermissions()
    {
        if (!Auth::check()) {
            return collect();
        }

        return Auth::user()->getAllPermissions();
    }

    /**
     * Check if user is super admin (has all permissions)
     *
     * @return bool
     */
    public static function isSuperAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('super-admin');
    }

    /**
     * Get all available roles
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllRoles()
    {
        return Role::all();
    }

    /**
     * Get all available permissions
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllPermissions()
    {
        return Permission::all();
    }

    /**
     * Check if a role exists
     *
     * @param string $role
     * @return bool
     */
    public static function roleExists(string $role): bool
    {
        return Role::where('name', $role)->exists();
    }

    /**
     * Check if a permission exists
     *
     * @param string $permission
     * @return bool
     */
    public static function permissionExists(string $permission): bool
    {
        return Permission::where('name', $permission)->exists();
    }

    /**
     * Create a new role
     *
     * @param string $name
     * @param string|null $guardName
     * @return Role
     */
    public static function createRole(string $name, string $guardName = null): Role
    {
        return Role::create([
            'name' => $name,
            'guard_name' => $guardName ?? 'web'
        ]);
    }

    /**
     * Create a new permission
     *
     * @param string $name
     * @param string|null $guardName
     * @return Permission
     */
    public static function createPermission(string $name, string $guardName = null): Permission
    {
        return Permission::create([
            'name' => $name,
            'guard_name' => $guardName ?? 'web'
        ]);
    }

    /**
     * Assign role to current user
     *
     * @param string $role
     * @return bool
     */
    public static function assignRole(string $role): bool
    {
        if (!Auth::check()) {
            return false;
        }

        Auth::user()->assignRole($role);
        return true;
    }

    /**
     * Remove role from current user
     *
     * @param string $role
     * @return bool
     */
    public static function removeRole(string $role): bool
    {
        if (!Auth::check()) {
            return false;
        }

        Auth::user()->removeRole($role);
        return true;
    }

    /**
     * Give permission to current user
     *
     * @param string $permission
     * @return bool
     */
    public static function givePermission(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }

        Auth::user()->givePermissionTo($permission);
        return true;
    }

    /**
     * Revoke permission from current user
     *
     * @param string $permission
     * @return bool
     */
    public static function revokePermission(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }

        Auth::user()->revokePermissionTo($permission);
        return true;
    }
}