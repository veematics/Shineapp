<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Gates based on permissions
        $this->registerPermissionGates();
    }

    /**
     * Register Gates for all permissions
     */
    private function registerPermissionGates(): void
    {
        try {
            Permission::get()->map(function ($permission) {
                Gate::define($permission->name, function ($user) use ($permission) {
                    return $user->hasPermissionTo($permission);
                });
            });
        } catch (\Exception $e) {
            // Handle case where permission table doesn't exist yet
            // This can happen during initial migration
        }

        // Define some common gates
        Gate::define('manage-users', function ($user) {
            return $user->hasPermissionTo('manage users') || $user->hasRole(['admin', 'super-admin']);
        });

        Gate::define('manage-roles', function ($user) {
            return $user->hasPermissionTo('manage roles') || $user->hasRole(['admin', 'super-admin']);
        });

        Gate::define('manage-permissions', function ($user) {
            return $user->hasPermissionTo('manage permissions') || $user->hasRole(['admin', 'super-admin']);
        });

        Gate::define('manage-models', function ($user) {
            return $user->hasPermissionTo('manage models') || $user->hasRole(['admin', 'super-admin']);
        });

        Gate::define('admin-access', function ($user) {
            return $user->hasRole(['admin', 'super-admin']);
        });

        Gate::define('super-admin-access', function ($user) {
            return $user->hasRole('super-admin');
        });
    }
}
