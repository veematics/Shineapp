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
            'impersonate users',
            
            // Role management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'manage roles',
            'assign roles',
            
            // Permission management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'manage permissions',
            
            // Content management
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',
            'edit own posts',
            'delete own posts',
            
            // Category management
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Media management
            'view media',
            'upload media',
            'edit media',
            'delete media',
            
            // Comment management
            'view comments',
            'moderate comments',
            'delete comments',
            
            // Dashboard access
            'view dashboard',
            'admin dashboard',
            'analytics dashboard',
            
            // Settings
            'view settings',
            'edit settings',
            'manage settings',
            'manage appsetting',
            'system settings',
            
            // Reports
            'view reports',
            'create reports',
            'export reports',
            'financial reports',
            
            // System administration
            'system administration',
            'backup system',
            'restore system',
            'view logs',
            'clear cache',
            
            // API access
            'api access',
            'api write',
            
            // Notifications
            'send notifications',
            'manage notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin - has all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // Admin - has most permissions except system administration
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminPermissions = [
            'view users', 'create users', 'edit users', 'delete users', 'manage users', 'impersonate users',
            'view roles', 'create roles', 'edit roles', 'delete roles', 'manage roles', 'assign roles',
            'view permissions', 'create permissions', 'edit permissions', 'delete permissions', 'manage permissions',
            'view posts', 'create posts', 'edit posts', 'delete posts', 'publish posts',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view media', 'upload media', 'edit media', 'delete media',
            'view comments', 'moderate comments', 'delete comments',
            'view dashboard', 'admin dashboard', 'analytics dashboard',
            'view settings', 'edit settings', 'manage settings',
            'view reports', 'create reports', 'export reports',
            'send notifications', 'manage notifications',
            'api access',
        ];
        $adminRole->syncPermissions($adminPermissions);

        // Content Manager - manages all content
        $contentManagerRole = Role::firstOrCreate(['name' => 'content-manager']);
        $contentManagerPermissions = [
            'view users',
            'view posts', 'create posts', 'edit posts', 'delete posts', 'publish posts',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view media', 'upload media', 'edit media', 'delete media',
            'view comments', 'moderate comments', 'delete comments',
            'view dashboard', 'analytics dashboard',
            'view settings',
            'view reports', 'create reports',
            'send notifications',
        ];
        $contentManagerRole->syncPermissions($contentManagerPermissions);

        // Editor - can create and edit content
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorPermissions = [
            'view posts', 'create posts', 'edit posts', 'edit own posts', 'delete own posts',
            'view categories',
            'view media', 'upload media',
            'view comments',
            'view dashboard',
            'view settings',
        ];
        $editorRole->syncPermissions($editorPermissions);

        // Author - can create own content
        $authorRole = Role::firstOrCreate(['name' => 'author']);
        $authorPermissions = [
            'view posts', 'create posts', 'edit own posts', 'delete own posts',
            'view categories',
            'view media', 'upload media',
            'view dashboard',
        ];
        $authorRole->syncPermissions($authorPermissions);

        // Moderator - can moderate comments and content
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        $moderatorPermissions = [
            'view posts', 'edit posts',
            'view comments', 'moderate comments', 'delete comments',
            'view dashboard',
        ];
        $moderatorRole->syncPermissions($moderatorPermissions);

        // Customer Support - can view users and send notifications
        $supportRole = Role::firstOrCreate(['name' => 'support']);
        $supportPermissions = [
            'view users',
            'view dashboard',
            'send notifications',
        ];
        $supportRole->syncPermissions($supportPermissions);

        // User - basic permissions
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userPermissions = [
            'view dashboard',
        ];
        $userRole->syncPermissions($userPermissions);

        // Create sample users with different roles
        
        // Super Admin user
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('admin'),
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole('super-admin');

        // Admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');

        // Content Manager user
        $contentManagerUser = User::firstOrCreate(
            ['email' => 'content@example.com'],
            [
                'name' => 'Content Manager',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $contentManagerUser->assignRole('content-manager');

        // Editor user
        $editorUser = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'John Editor',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $editorUser->assignRole('editor');

        // Author user
        $authorUser = User::firstOrCreate(
            ['email' => 'author@example.com'],
            [
                'name' => 'Jane Author',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $authorUser->assignRole('author');

        // Moderator user
        $moderatorUser = User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Mike Moderator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $moderatorUser->assignRole('moderator');

        // Support user
        $supportUser = User::firstOrCreate(
            ['email' => 'support@example.com'],
            [
                'name' => 'Sarah Support',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $supportUser->assignRole('support');

        // Regular users
        $regularUser1 = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $regularUser1->assignRole('user');

        $regularUser2 = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $regularUser2->assignRole('user');

        // Demo user with multiple roles
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => bcrypt('demo'),
                'email_verified_at' => now(),
            ]
        );
        $demoUser->assignRole(['author', 'moderator']);

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Created users with roles:');
        $this->command->info('- Super Admin: admin@example.com (password: admin)');
        $this->command->info('- Admin: admin@admin.com (password: password)');
        $this->command->info('- Content Manager: content@example.com (password: password)');
        $this->command->info('- Editor: editor@example.com (password: password)');
        $this->command->info('- Author: author@example.com (password: password)');
        $this->command->info('- Moderator: moderator@example.com (password: password)');
        $this->command->info('- Support: support@example.com (password: password)');
        $this->command->info('- User: user@example.com (password: password)');
        $this->command->info('- Customer: customer@example.com (password: password)');
        $this->command->info('- Demo (Author + Moderator): demo@example.com (password: demo)');
    }
}