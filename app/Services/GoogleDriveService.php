<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class GoogleDriveService
{
    protected Client $client;
    protected Drive $service;
    protected array $config;

    public function __construct()
    {
        $this->config = config('googledrive');
        $this->initializeClient();
    }

    /**
     * Initialize Google Client
     */
    protected function initializeClient(): void
    {
        $this->client = new Client();
        
        // Set OAuth2 credentials
        if ($this->config['client_id'] && $this->config['client_secret']) {
            $this->client->setClientId($this->config['client_id']);
            $this->client->setClientSecret($this->config['client_secret']);
            $this->client->setRedirectUri($this->config['redirect_uri']);
        }
        
        // Set service account if available
        if ($this->config['service_account_path'] && file_exists($this->config['service_account_path'])) {
            $this->client->setAuthConfig($this->config['service_account_path']);
        }
        
        // Set scopes
        $this->client->setScopes($this->config['scopes']);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        
        $this->service = new Drive($this->client);
    }

    /**
     * Get OAuth2 authorization URL
     */
    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Handle OAuth2 callback and store tokens
     */
    public function handleCallback(string $code): array
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($token['error'])) {
            throw new Exception('OAuth2 Error: ' . $token['error_description']);
        }
        
        // Store tokens in cache or database
        $this->storeTokens($token);
        
        return $token;
    }

    /**
     * Set access token
     */
    public function setAccessToken(array $token): void
    {
        $this->client->setAccessToken($token);
        
        // Refresh token if expired
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $this->client->getRefreshToken();
            if ($refreshToken) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                $this->storeTokens($newToken);
            }
        }
    }

    /**
     * Upload file to Google Drive
     */
    public function uploadFile(UploadedFile $file, string $folderId = null, array $metadata = []): array
    {
        try {
            // Validate file
            $this->validateFile($file);
            
            // Create file metadata
            $fileMetadata = new DriveFile([
                'name' => $metadata['name'] ?? $file->getClientOriginalName(),
                'parents' => $folderId ? [$folderId] : null,
                'description' => $metadata['description'] ?? null,
            ]);
            
            // Upload file
            $result = $this->service->files->create(
                $fileMetadata,
                [
                    'data' => file_get_contents($file->getPathname()),
                    'mimeType' => $file->getMimeType(),
                    'uploadType' => 'multipart',
                    'fields' => 'id,name,mimeType,size,createdTime,modifiedTime,webViewLink,webContentLink'
                ]
            );
            
            Log::info('File uploaded to Google Drive', ['file_id' => $result->getId(), 'name' => $result->getName()]);
            
            return [
                'success' => true,
                'file' => [
                    'id' => $result->getId(),
                    'name' => $result->getName(),
                    'mimeType' => $result->getMimeType(),
                    'size' => $result->getSize(),
                    'createdTime' => $result->getCreatedTime(),
                    'modifiedTime' => $result->getModifiedTime(),
                    'webViewLink' => $result->getWebViewLink(),
                    'webContentLink' => $result->getWebContentLink(),
                ]
            ];
            
        } catch (Exception $e) {
            Log::error('Google Drive upload failed', ['error' => $e->getMessage()]);
            throw new Exception('Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Download file from Google Drive
     */
    public function downloadFile(string $fileId): array
    {
        try {
            $file = $this->service->files->get($fileId, ['fields' => 'id,name,mimeType,size']);
            $content = $this->service->files->get($fileId, ['alt' => 'media']);
            
            return [
                'success' => true,
                'file' => [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'mimeType' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'content' => $content->getBody()->getContents()
                ]
            ];
            
        } catch (Exception $e) {
            Log::error('Google Drive download failed', ['file_id' => $fileId, 'error' => $e->getMessage()]);
            throw new Exception('Failed to download file: ' . $e->getMessage());
        }
    }

    /**
     * List files in Google Drive
     */
    public function listFiles(string $folderId = null, int $pageSize = 10, string $pageToken = null): array
    {
        try {
            $query = $folderId ? "'$folderId' in parents" : null;
            
            $parameters = [
                'pageSize' => $pageSize,
                'fields' => 'nextPageToken, files(id,name,mimeType,size,createdTime,modifiedTime,webViewLink,parents)',
                'orderBy' => 'modifiedTime desc'
            ];
            
            if ($query) {
                $parameters['q'] = $query;
            }
            
            if ($pageToken) {
                $parameters['pageToken'] = $pageToken;
            }
            
            $result = $this->service->files->listFiles($parameters);
            
            return [
                'success' => true,
                'files' => array_map(function($file) {
                    return [
                        'id' => $file->getId(),
                        'name' => $file->getName(),
                        'mimeType' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'createdTime' => $file->getCreatedTime(),
                        'modifiedTime' => $file->getModifiedTime(),
                        'webViewLink' => $file->getWebViewLink(),
                        'parents' => $file->getParents(),
                        'isFolder' => $file->getMimeType() === 'application/vnd.google-apps.folder'
                    ];
                }, $result->getFiles()),
                'nextPageToken' => $result->getNextPageToken()
            ];
            
        } catch (Exception $e) {
            Log::error('Google Drive list files failed', ['error' => $e->getMessage()]);
            throw new Exception('Failed to list files: ' . $e->getMessage());
        }
    }

    /**
     * Delete file from Google Drive
     */
    public function deleteFile(string $fileId): array
    {
        try {
            $this->service->files->delete($fileId);
            
            Log::info('File deleted from Google Drive', ['file_id' => $fileId]);
            
            return ['success' => true, 'message' => 'File deleted successfully'];
            
        } catch (Exception $e) {
            Log::error('Google Drive delete failed', ['file_id' => $fileId, 'error' => $e->getMessage()]);
            throw new Exception('Failed to delete file: ' . $e->getMessage());
        }
    }

    /**
     * Create folder in Google Drive
     */
    public function createFolder(string $name, string $parentId = null): array
    {
        try {
            $fileMetadata = new DriveFile([
                'name' => $name,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => $parentId ? [$parentId] : null
            ]);
            
            $folder = $this->service->files->create($fileMetadata, [
                'fields' => 'id,name,createdTime,modifiedTime,webViewLink'
            ]);
            
            Log::info('Folder created in Google Drive', ['folder_id' => $folder->getId(), 'name' => $folder->getName()]);
            
            return [
                'success' => true,
                'folder' => [
                    'id' => $folder->getId(),
                    'name' => $folder->getName(),
                    'createdTime' => $folder->getCreatedTime(),
                    'modifiedTime' => $folder->getModifiedTime(),
                    'webViewLink' => $folder->getWebViewLink()
                ]
            ];
            
        } catch (Exception $e) {
            Log::error('Google Drive create folder failed', ['name' => $name, 'error' => $e->getMessage()]);
            throw new Exception('Failed to create folder: ' . $e->getMessage());
        }
    }

    /**
     * Get file information
     */
    public function getFileInfo(string $fileId): array
    {
        try {
            $file = $this->service->files->get($fileId, [
                'fields' => 'id,name,mimeType,size,createdTime,modifiedTime,webViewLink,webContentLink,parents,owners,permissions'
            ]);
            
            return [
                'success' => true,
                'file' => [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'mimeType' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'createdTime' => $file->getCreatedTime(),
                    'modifiedTime' => $file->getModifiedTime(),
                    'webViewLink' => $file->getWebViewLink(),
                    'webContentLink' => $file->getWebContentLink(),
                    'parents' => $file->getParents(),
                    'owners' => $file->getOwners(),
                    'permissions' => $file->getPermissions(),
                    'isFolder' => $file->getMimeType() === 'application/vnd.google-apps.folder'
                ]
            ];
            
        } catch (Exception $e) {
            Log::error('Google Drive get file info failed', ['file_id' => $fileId, 'error' => $e->getMessage()]);
            throw new Exception('Failed to get file info: ' . $e->getMessage());
        }
    }

    /**
     * Search files in Google Drive
     */
    public function searchFiles(string $query, int $pageSize = 10): array
    {
        try {
            $result = $this->service->files->listFiles([
                'q' => "name contains '$query'",
                'pageSize' => $pageSize,
                'fields' => 'files(id,name,mimeType,size,createdTime,modifiedTime,webViewLink)',
                'orderBy' => 'modifiedTime desc'
            ]);
            
            return [
                'success' => true,
                'files' => array_map(function($file) {
                    return [
                        'id' => $file->getId(),
                        'name' => $file->getName(),
                        'mimeType' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'createdTime' => $file->getCreatedTime(),
                        'modifiedTime' => $file->getModifiedTime(),
                        'webViewLink' => $file->getWebViewLink(),
                        'isFolder' => $file->getMimeType() === 'application/vnd.google-apps.folder'
                    ];
                }, $result->getFiles())
            ];
            
        } catch (Exception $e) {
            Log::error('Google Drive search failed', ['query' => $query, 'error' => $e->getMessage()]);
            throw new Exception('Failed to search files: ' . $e->getMessage());
        }
    }

    /**
     * Validate uploaded file
     */
    protected function validateFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > $this->config['max_file_size']) {
            throw new Exception('File size exceeds maximum allowed size.');
        }
        
        // Check MIME type
        if (!in_array($file->getMimeType(), $this->config['allowed_mime_types'])) {
            throw new Exception('File type not allowed.');
        }
    }

    /**
     * Store OAuth2 tokens
     */
    protected function storeTokens(array $token): void
    {
        // Store in cache (you might want to store in database for production)
        Cache::put('googledrive_access_token', $token, $this->config['cache_ttl']);
    }

    /**
     * Get stored tokens
     */
    public function getStoredTokens(): ?array
    {
        return Cache::get('googledrive_access_token');
    }

    /**
     * Check if authenticated
     */
    public function isAuthenticated(): bool
    {
        $token = $this->getStoredTokens();
        if (!$token) {
            return false;
        }
        
        $this->setAccessToken($token);
        return !$this->client->isAccessTokenExpired();
    }

    /**
     * Get client instance
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Get service instance
     */
    public function getService(): Drive
    {
        return $this->service;
    }
}