<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Registered Models for Permission Management
    |--------------------------------------------------------------------------
    |
    | This configuration defines which models can be managed through the
    | admin/models interface for role and permission assignment.
    |
    */

    'registered_models' => [
        'User' => [
            'class' => 'App\Models\User',
            'display_name' => 'Users',
            'description' => 'System users and their accounts',
            'permissions' => [
                'view users',
                'create users',
                'edit users',
                'delete users',
                'manage users'
            ]
        ],
        'Role' => [
            'class' => 'Spatie\Permission\Models\Role',
            'display_name' => 'Roles',
            'description' => 'User roles and access levels',
            'permissions' => [
                'view roles',
                'create roles',
                'edit roles',
                'delete roles',
                'assign roles'
            ]
        ],
        'Permission' => [
            'class' => 'Spatie\Permission\Models\Permission',
            'display_name' => 'Permissions',
            'description' => 'System permissions and access rights',
            'permissions' => [
                'view permissions',
                'create permissions',
                'edit permissions',
                'delete permissions',
                'manage permissions'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Naming Convention
    |--------------------------------------------------------------------------
    |
    | Define the naming convention for auto-generated permissions.
    | Available placeholders: {action}, {model}, {model_lower}
    |
    */

    'permission_naming' => [
        'pattern' => '{action} {model_lower}',
        'actions' => ['view', 'create', 'edit', 'delete', 'manage']
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Discovery Settings
    |--------------------------------------------------------------------------
    |
    | Configure how the system discovers and registers models automatically.
    |
    */

    'discovery' => [
        'enabled' => true,
        'directories' => [
            app_path('Models')
        ],
        'exclude' => [
            // Models to exclude from automatic discovery
        ],
        'auto_register_permissions' => true
    ]
];