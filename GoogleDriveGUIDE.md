# Google Drive Integration Guide

This guide provides comprehensive documentation for the Google Drive integration in your Laravel application. The integration allows users to connect their Google Drive accounts and manage files directly from the admin panel.

## Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Installation & Setup](#installation--setup)
4. [Configuration](#configuration)
5. [Usage](#usage)
6. [API Endpoints](#api-endpoints)
7. [Components Overview](#components-overview)
8. [Security Considerations](#security-considerations)
9. [Troubleshooting](#troubleshooting)
10. [Advanced Usage](#advanced-usage)

## Overview

The Google Drive integration provides:

- **OAuth2 Authentication** - Secure connection to Google Drive accounts
- **File Management** - Upload, download, delete, and organize files
- **Folder Operations** - Create and navigate through folders
- **Search Functionality** - Search files across Google Drive
- **Admin Interface** - Modern CoreUI-styled management panel
- **Permission Control** - Access controlled by Laravel permissions

## Prerequisites

- Laravel 10+ application
- Google API Client Library (`google/apiclient: ^2.18`)
- Spatie Laravel Permission package
- CoreUI Admin Template
- Valid Google Cloud Project with Drive API enabled

## Installation & Setup

### 1. Google Cloud Console Setup

1. **Create a Google Cloud Project**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select an existing one

2. **Enable Google Drive API**
   ```bash
   # Navigate to APIs & Services > Library
   # Search for "Google Drive API" and enable it
   ```

3. **Create OAuth2 Credentials**
   - Go to APIs & Services > Credentials
   - Click "Create Credentials" > "OAuth client ID"
   - Choose "Web application"
   - Add authorized redirect URIs:
     - `http://localhost:8000/admin/googledrive/callback` (development)
     - `https://yourdomain.com/admin/googledrive/callback` (production)

4. **Download Credentials**
   - Download the JSON file containing your client ID and secret

### 2. Environment Configuration

Add the following variables to your `.env` file:

```env
# Google Drive API Configuration
GOOGLE_DRIVE_CLIENT_ID=your_google_client_id_here
GOOGLE_DRIVE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_DRIVE_REDIRECT_URI=http://localhost:8000/admin/googledrive/callback
GOOGLE_DRIVE_SERVICE_ACCOUNT_PATH=storage/app/google-service-account.json
GOOGLE_DRIVE_DEFAULT_FOLDER_ID=
GOOGLE_DRIVE_MAX_FILE_SIZE=104857600
GOOGLE_DRIVE_CACHE_TTL=3600
GOOGLE_DRIVE_WEBHOOK_URL=
GOOGLE_DRIVE_WEBHOOK_SECRET=
```

### 3. Permission Setup

Ensure users have the `manage appsetting` permission to access Google Drive features:

```php
// Grant permission to a user
$user->givePermissionTo('manage appsetting');

// Or assign a role that has this permission
$user->assignRole('admin');
```

## Configuration

### Configuration File: `config/googledrive.php`

```php
return [
    // OAuth2 Credentials
    'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
    'redirect_uri' => env('GOOGLE_DRIVE_REDIRECT_URI'),
    
    // File Upload Settings
    'max_file_size' => env('GOOGLE_DRIVE_MAX_FILE_SIZE', 104857600), // 100MB
    'allowed_mime_types' => [
        'image/*', 'video/*', 'audio/*',
        'application/pdf', 'text/*',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.*'
    ],
    
    // API Settings
    'scopes' => [
        'https://www.googleapis.com/auth/drive.file',
        'https://www.googleapis.com/auth/drive.metadata.readonly'
    ],
    
    // Cache Settings
    'cache_ttl' => env('GOOGLE_DRIVE_CACHE_TTL', 3600),
];
```

### Key Configuration Options

- **`max_file_size`** - Maximum file size for uploads (bytes)
- **`allowed_mime_types`** - Allowed file types for upload
- **`scopes`** - Google Drive API scopes required
- **`cache_ttl`** - Cache duration for API responses

## Usage

### Accessing the Interface

1. Navigate to `/admin/googledrive` in your application
2. If not authenticated, click "Connect to Google Drive"
3. Complete the OAuth2 flow
4. Start managing files

### Basic Operations

#### Upload Files
1. Click "Upload File" button
2. Select file from your computer
3. Optionally set custom name and description
4. Click "Upload"

#### Create Folders
1. Click "Create Folder" button
2. Enter folder name
3. Click "Create"

#### Navigate Folders
- Click on folder names to enter them
- Use breadcrumb navigation to go back
- Click "Root" to return to the main directory

#### Search Files
1. Enter search terms in the search box
2. Click search button or press Enter
3. Results will display matching files

#### File Actions
- **Info** - View detailed file information
- **Download** - Download file to your computer
- **Delete** - Remove file from Google Drive

## API Endpoints

### Authentication Endpoints

```php
// Get authentication URL
GET /admin/googledrive/auth

// Handle OAuth callback
GET /admin/googledrive/callback

// Check authentication status
GET /admin/googledrive/status

// Disconnect from Google Drive
POST /admin/googledrive/disconnect
```

### File Management Endpoints

```php
// List files
GET /admin/googledrive/files
Parameters: folder_id, page_token

// Upload file
POST /admin/googledrive/files/upload
Parameters: file, name, description, folder_id

// Download file
GET /admin/googledrive/files/{fileId}/download

// Delete file
DELETE /admin/googledrive/files/{fileId}

// Get file info
GET /admin/googledrive/files/{fileId}/info

// Search files
GET /admin/googledrive/search
Parameters: query

// Create folder
POST /admin/googledrive/folders/create
Parameters: name, parent_id
```

## Components Overview

### Core Classes

#### `GoogleDriveService`
Main service class handling Google Drive operations:

```php
use App\Services\GoogleDriveService;

$service = new GoogleDriveService();

// Upload a file
$result = $service->uploadFile($filePath, $fileName, $folderId);

// List files
$files = $service->listFiles($folderId, $pageToken);

// Search files
$results = $service->searchFiles($query);
```

#### `GoogleDriveAuth`
OAuth2 authentication helper:

```php
use App\Helpers\GoogleDriveAuth;

$auth = new GoogleDriveAuth();

// Get authorization URL
$authUrl = $auth->getAuthUrl();

// Handle callback
$auth->handleCallback($code);

// Check if authenticated
if ($auth->isAuthenticated()) {
    // User is connected
}
```

#### `GoogleDriveController`
Handles HTTP requests and responses:

```php
// Display dashboard
public function index()

// Handle authentication
public function authenticate()
public function callback(Request $request)

// File operations
public function listFiles(Request $request)
public function uploadFile(Request $request)
public function downloadFile($fileId)
```

### Frontend Components

The admin interface (`resources/views/admin/googledrive/index.blade.php`) includes:

- **File Browser** - Table-based file listing with icons
- **Upload Modal** - File upload form with validation
- **Folder Modal** - Folder creation form
- **Info Modal** - Detailed file information display
- **Search Interface** - Real-time file search
- **Breadcrumb Navigation** - Folder hierarchy navigation

## Security Considerations

### Authentication & Authorization

1. **OAuth2 Flow** - Secure token-based authentication
2. **Permission Control** - Access restricted by `manage appsetting` permission
3. **Token Storage** - Encrypted token storage in user sessions
4. **CSRF Protection** - All forms protected by Laravel CSRF tokens

### File Validation

```php
// File size validation
if ($file->getSize() > config('googledrive.max_file_size')) {
    throw new Exception('File too large');
}

// MIME type validation
$allowedTypes = config('googledrive.allowed_mime_types');
if (!$this->isAllowedMimeType($file->getMimeType(), $allowedTypes)) {
    throw new Exception('File type not allowed');
}
```

### Best Practices

1. **Environment Variables** - Never commit API credentials to version control
2. **Token Refresh** - Automatic token refresh handling
3. **Error Handling** - Comprehensive error handling and logging
4. **Rate Limiting** - Respect Google API rate limits
5. **Data Validation** - Validate all user inputs

## Troubleshooting

### Common Issues

#### "Invalid Credentials" Error
- Verify `GOOGLE_DRIVE_CLIENT_ID` and `GOOGLE_DRIVE_CLIENT_SECRET`
- Check redirect URI matches Google Cloud Console settings
- Ensure Google Drive API is enabled

#### "Permission Denied" Error
- Verify user has `manage appsetting` permission
- Check OAuth2 scopes in configuration
- Ensure user completed authentication flow

#### "File Upload Failed" Error
- Check file size against `max_file_size` setting
- Verify file type is in `allowed_mime_types`
- Check Google Drive storage quota

#### "Token Expired" Error
- Tokens are automatically refreshed
- If persistent, re-authenticate the user
- Check token storage mechanism

### Debug Mode

Enable debug logging in your `.env`:

```env
LOG_LEVEL=debug
```

Check logs in `storage/logs/laravel.log` for detailed error information.

### Testing Connection

Test your Google Drive connection:

```php
// In tinker or a test route
use App\Services\GoogleDriveService;

$service = new GoogleDriveService();
try {
    $files = $service->listFiles();
    dd('Connection successful', $files);
} catch (Exception $e) {
    dd('Connection failed', $e->getMessage());
}
```

## Advanced Usage

### Custom File Operations

Extend the `GoogleDriveService` for custom operations:

```php
class CustomGoogleDriveService extends GoogleDriveService
{
    public function shareFile($fileId, $email, $role = 'reader')
    {
        $client = $this->initializeClient();
        $service = new Google_Service_Drive($client);
        
        $permission = new Google_Service_Drive_Permission();
        $permission->setType('user');
        $permission->setRole($role);
        $permission->setEmailAddress($email);
        
        return $service->permissions->create($fileId, $permission);
    }
}
```

### Webhook Integration

Set up webhooks for real-time file change notifications:

```php
// In your controller
public function handleWebhook(Request $request)
{
    $signature = $request->header('X-Goog-Channel-Token');
    $expectedSignature = config('googledrive.webhook_secret');
    
    if ($signature !== $expectedSignature) {
        abort(403, 'Invalid webhook signature');
    }
    
    // Process webhook data
    $changeType = $request->header('X-Goog-Resource-State');
    $resourceId = $request->header('X-Goog-Resource-ID');
    
    // Handle the change
    $this->handleFileChange($changeType, $resourceId);
    
    return response('OK', 200);
}
```

### Batch Operations

Perform batch operations for better performance:

```php
public function batchDeleteFiles(array $fileIds)
{
    $client = $this->initializeClient();
    $client->setUseBatch(true);
    
    $batch = $client->createBatch();
    $service = new Google_Service_Drive($client);
    
    foreach ($fileIds as $fileId) {
        $request = $service->files->delete($fileId);
        $batch->add($request, $fileId);
    }
    
    return $batch->execute();
}
```

### Custom UI Components

Create custom UI components for specific needs:

```blade
{{-- Custom file picker component --}}
<div class="google-drive-picker" data-folder-id="{{ $folderId }}">
    <button class="btn btn-primary" onclick="openFilePicker()">
        <i class="cil-folder-open me-2"></i>
        Select from Google Drive
    </button>
</div>

<script>
function openFilePicker() {
    // Custom file picker implementation
    fetch('/admin/googledrive/files')
        .then(response => response.json())
        .then(data => {
            // Display files in custom modal
            showFilePickerModal(data.files);
        });
}
</script>
```

## Performance Optimization

### Caching

Implement caching for frequently accessed data:

```php
use Illuminate\Support\Facades\Cache;

public function listFiles($folderId = null, $pageToken = null)
{
    $cacheKey = "googledrive_files_{$folderId}_{$pageToken}";
    
    return Cache::remember($cacheKey, config('googledrive.cache_ttl'), function () use ($folderId, $pageToken) {
        return $this->fetchFilesFromAPI($folderId, $pageToken);
    });
}
```

### Pagination

Implement proper pagination for large file lists:

```php
public function listFiles($folderId = null, $pageSize = 50, $pageToken = null)
{
    $client = $this->initializeClient();
    $service = new Google_Service_Drive($client);
    
    $parameters = [
        'pageSize' => $pageSize,
        'fields' => 'nextPageToken, files(id, name, mimeType, size, modifiedTime)'
    ];
    
    if ($folderId) {
        $parameters['q'] = "'{$folderId}' in parents";
    }
    
    if ($pageToken) {
        $parameters['pageToken'] = $pageToken;
    }
    
    return $service->files->listFiles($parameters);
}
```

---

## Support

For additional support:

1. Check the [Google Drive API Documentation](https://developers.google.com/drive/api/v3/reference)
2. Review Laravel logs for detailed error information
3. Ensure all dependencies are up to date
4. Test with a fresh Google Cloud project if issues persist

## License

This Google Drive integration is part of your Laravel application and follows the same licensing terms.