<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage users',
            
            // Role management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'manage roles',
            
            // Permission management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'manage permissions',
            
            // Model management
            'manage models',
            
            // Dashboard access
            'view dashboard',
            'admin dashboard',
            
            // Settings
            'view settings',
            'edit settings',
            'manage settings',
            
            // Reports
            'view reports',
            'create reports',
            'export reports',
            
            // System administration
            'system administration',
            'backup system',
            'restore system',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin - has all permissions
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin - has most permissions except system administration
        $adminRole = Role::create(['name' => 'admin']);
        $adminPermissions = [
            'view users', 'create users', 'edit users', 'delete users', 'manage users',
            'view roles', 'create roles', 'edit roles', 'delete roles', 'manage roles',
            'view permissions', 'manage permissions',
            'manage models',
            'view dashboard', 'admin dashboard',
            'view settings', 'edit settings', 'manage settings',
            'view reports', 'create reports', 'export reports',
        ];
        $adminRole->givePermissionTo($adminPermissions);

        // Manager - can manage users and view reports
        $managerRole = Role::create(['name' => 'manager']);
        $managerPermissions = [
            'view users', 'create users', 'edit users', 'manage users',
            'view dashboard',
            'view settings',
            'view reports', 'create reports',
        ];
        $managerRole->givePermissionTo($managerPermissions);

        // Editor - can edit content and view reports
        $editorRole = Role::create(['name' => 'editor']);
        $editorPermissions = [
            'view users',
            'view dashboard',
            'view settings',
            'view reports',
        ];
        $editorRole->givePermissionTo($editorPermissions);

        // User - basic permissions
        $userRole = Role::create(['name' => 'user']);
        $userPermissions = [
            'view dashboard',
        ];
        $userRole->givePermissionTo($userPermissions);

        // Create a super admin user if it doesn't exist
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole('super-admin');

        // Create a regular admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');

        // Create a manager user
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $managerUser->assignRole('manager');

        // Create a regular user
        $regularUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $regularUser->assignRole('user');

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Created users:');
        $this->command->info('- Super Admin: admin@example.com (password: password)');
        $this->command->info('- Admin: admin@admin.com (password: password)');
        $this->command->info('- Manager: manager@example.com (password: password)');
        $this->command->info('- User: user@example.com (password: password)');
    }
}