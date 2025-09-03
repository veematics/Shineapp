# Spatie Laravel-Permission Sample Data Guide

This guide explains the comprehensive sample data created for the Spatie Laravel-Permission package in this Laravel application.

## Overview

The `RolePermissionSeeder` creates a complete permission system with:
- **65+ Permissions** across different modules
- **8 Roles** with different access levels
- **10 Sample Users** with various role assignments

## Permissions Structure

### User Management
- `view users` - View user listings
- `create users` - Create new users
- `edit users` - Edit user information
- `delete users` - Delete users
- `manage users` - Full user management
- `impersonate users` - Login as other users

### Role & Permission Management
- `view roles` - View role listings
- `create roles` - Create new roles
- `edit roles` - Edit role information
- `delete roles` - Delete roles
- `manage roles` - Full role management
- `assign roles` - Assign roles to users
- `view permissions` - View permission listings
- `create permissions` - Create new permissions
- `edit permissions` - Edit permission information
- `delete permissions` - Delete permissions
- `manage permissions` - Full permission management

### Content Management
- `view posts` - View post listings
- `create posts` - Create new posts
- `edit posts` - Edit any posts
- `delete posts` - Delete any posts
- `publish posts` - Publish/unpublish posts
- `edit own posts` - Edit only own posts
- `delete own posts` - Delete only own posts

### Category Management
- `view categories` - View category listings
- `create categories` - Create new categories
- `edit categories` - Edit categories
- `delete categories` - Delete categories

### Media Management
- `view media` - View media library
- `upload media` - Upload new media files
- `edit media` - Edit media information
- `delete media` - Delete media files

### Comment Management
- `view comments` - View comment listings
- `moderate comments` - Approve/reject comments
- `delete comments` - Delete comments

### Dashboard Access
- `view dashboard` - Access basic dashboard
- `admin dashboard` - Access admin dashboard
- `analytics dashboard` - Access analytics dashboard

### Settings Management
- `view settings` - View application settings
- `edit settings` - Edit basic settings
- `manage settings` - Full settings management
- `system settings` - Manage system-level settings

### Reports
- `view reports` - View report listings
- `create reports` - Generate new reports
- `export reports` - Export reports to files
- `financial reports` - Access financial reports

### System Administration
- `system administration` - Full system admin access
- `backup system` - Create system backups
- `restore system` - Restore from backups
- `view logs` - View system logs
- `clear cache` - Clear application cache

### API Access
- `api access` - Basic API access
- `api write` - API write permissions

### Notifications
- `send notifications` - Send notifications to users
- `manage notifications` - Full notification management

## Roles Structure

### 1. Super Admin (`super-admin`)
- **Access**: All permissions
- **Use Case**: System owner, full access to everything
- **Sample User**: admin@example.com (password: admin)

### 2. Admin (`admin`)
- **Access**: Most permissions except system administration
- **Use Case**: Site administrator, manages users, content, and settings
- **Sample User**: admin@admin.com (password: password)

### 3. Content Manager (`content-manager`)
- **Access**: Full content management, user viewing, reports
- **Use Case**: Manages all content, categories, media, and comments
- **Sample User**: content@example.com (password: password)

### 4. Editor (`editor`)
- **Access**: Create and edit content, manage media
- **Use Case**: Content creation and editing
- **Sample User**: editor@example.com (password: password)

### 5. Author (`author`)
- **Access**: Create and manage own content only
- **Use Case**: Content creators with limited scope
- **Sample User**: author@example.com (password: password)

### 6. Moderator (`moderator`)
- **Access**: Moderate comments and edit posts
- **Use Case**: Community moderation
- **Sample User**: moderator@example.com (password: password)

### 7. Support (`support`)
- **Access**: View users, send notifications
- **Use Case**: Customer support team
- **Sample User**: support@example.com (password: password)

### 8. User (`user`)
- **Access**: Basic dashboard access only
- **Use Case**: Regular application users
- **Sample Users**: 
  - user@example.com (password: password)
  - customer@example.com (password: password)

## Sample Users

| Email | Name | Role(s) | Password | Description |
|-------|------|---------|----------|-------------|
| admin@example.com | Super Admin | super-admin | admin | Full system access |
| admin@admin.com | System Admin | admin | password | Site administrator |
| content@example.com | Content Manager | content-manager | password | Content management |
| editor@example.com | John Editor | editor | password | Content editor |
| author@example.com | Jane Author | author | password | Content author |
| moderator@example.com | Mike Moderator | moderator | password | Community moderator |
| support@example.com | Sarah Support | support | password | Customer support |
| user@example.com | Regular User | user | password | Basic user |
| customer@example.com | Customer User | user | password | Customer user |
| demo@example.com | Demo User | author, moderator | demo | Multi-role demo |

## Usage Examples

### Checking Permissions in Controllers

```php
// Check if user has specific permission
if (auth()->user()->can('create posts')) {
    // User can create posts
}

// Check if user has role
if (auth()->user()->hasRole('admin')) {
    // User is an admin
}

// Check multiple permissions
if (auth()->user()->canAny(['edit posts', 'delete posts'])) {
    // User can either edit or delete posts
}
```

### Using Middleware

```php
// In routes/web.php
Route::middleware(['permission:manage users'])->group(function () {
    Route::resource('users', UserController::class);
});

// Role-based middleware
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});
```

### Using in Blade Templates

```blade
@can('create posts')
    <a href="{{ route('posts.create') }}" class="btn btn-primary">Create Post</a>
@endcan

@role('admin')
    <div class="admin-panel">
        <!-- Admin-only content -->
    </div>
@endrole

@hasanyrole('admin|content-manager')
    <div class="management-tools">
        <!-- Management tools -->
    </div>
@endhasanyrole
```

### Using Gates and Policies

```php
// In a Policy
public function update(User $user, Post $post)
{
    return $user->can('edit posts') || 
           ($user->can('edit own posts') && $user->id === $post->user_id);
}

// In Controller
public function update(Request $request, Post $post)
{
    $this->authorize('update', $post);
    // Update logic
}
```

## Running the Seeder

To populate your database with this sample data:

```bash
# Run the specific seeder
php artisan db:seed --class=RolePermissionSeeder

# Or run all seeders
php artisan db:seed
```

## Customization

You can modify the `RolePermissionSeeder` to:
- Add more permissions for your specific modules
- Create additional roles for your organization structure
- Add more sample users with different role combinations
- Adjust permission assignments for existing roles

## Best Practices

1. **Granular Permissions**: Use specific permissions rather than broad ones
2. **Role Hierarchy**: Design roles with clear hierarchies and responsibilities
3. **Own vs All**: Distinguish between managing own content vs all content
4. **Module-based**: Group permissions by functional modules
5. **Consistent Naming**: Use consistent naming conventions for permissions

This sample data provides a solid foundation for implementing a comprehensive permission system in your Laravel application using Spatie Laravel-Permission.