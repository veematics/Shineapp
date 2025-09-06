<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Drive API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for Google Drive API integration.
    | You need to create a project in Google Cloud Console and enable
    | Google Drive API to get the required credentials.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | OAuth2 Credentials
    |--------------------------------------------------------------------------
    |
    | These credentials are obtained from Google Cloud Console.
    | Create a new project, enable Google Drive API, and create OAuth2 credentials.
    |
    */
    'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
    'redirect_uri' => env('GOOGLE_DRIVE_REDIRECT_URI', env('APP_URL') . '/admin/googledrive/callback'),

    /*
    |--------------------------------------------------------------------------
    | Service Account (Optional)
    |--------------------------------------------------------------------------
    |
    | For server-to-server authentication, you can use a service account.
    | Download the JSON key file from Google Cloud Console.
    |
    */
    'service_account_path' => env('GOOGLE_DRIVE_SERVICE_ACCOUNT_PATH'),

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for Google Drive API behavior.
    |
    */
    'scopes' => [
        'https://www.googleapis.com/auth/drive',
        'https://www.googleapis.com/auth/drive.file',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Folder Settings
    |--------------------------------------------------------------------------
    |
    | Configure default behavior for file operations.
    |
    */
    'default_folder_id' => env('GOOGLE_DRIVE_DEFAULT_FOLDER_ID'), // Optional: specific folder ID
    'default_folder_name' => env('GOOGLE_DRIVE_DEFAULT_FOLDER_NAME', 'Laravel App Files'),

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Configure file upload behavior and limits.
    |
    */
    'max_file_size' => env('GOOGLE_DRIVE_MAX_FILE_SIZE', 100 * 1024 * 1024), // 100MB default
    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'text/plain',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configure caching for Google Drive operations.
    |
    */
    'cache_ttl' => env('GOOGLE_DRIVE_CACHE_TTL', 3600), // 1 hour default
    'cache_prefix' => 'googledrive_',

    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    |
    | Configure error handling behavior.
    |
    */
    'retry_attempts' => env('GOOGLE_DRIVE_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('GOOGLE_DRIVE_RETRY_DELAY', 1000), // milliseconds

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings (Optional)
    |--------------------------------------------------------------------------
    |
    | Configure webhooks for real-time file change notifications.
    |
    */
    'webhook_url' => env('GOOGLE_DRIVE_WEBHOOK_URL'),
    'webhook_secret' => env('GOOGLE_DRIVE_WEBHOOK_SECRET'),
];