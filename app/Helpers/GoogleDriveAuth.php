<?php

namespace App\Helpers;

use Google\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Exception;

class GoogleDriveAuth
{
    protected Client $client;
    protected array $config;
    protected string $cachePrefix;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->config = config('googledrive');
        $this->cachePrefix = $this->config['cache_prefix'] ?? 'googledrive_';
        $this->cacheTtl = $this->config['cache_ttl'] ?? 3600;
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
        if (isset($this->config['service_account_path']) && 
            $this->config['service_account_path'] && 
            file_exists($this->config['service_account_path'])) {
            $this->client->setAuthConfig($this->config['service_account_path']);
        }
        
        // Set scopes
        $this->client->setScopes($this->config['scopes'] ?? [
            'https://www.googleapis.com/auth/drive',
            'https://www.googleapis.com/auth/drive.file'
        ]);
        
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setApprovalPrompt('force');
    }

    /**
     * Get authorization URL
     */
    public function getAuthorizationUrl(string $state = null): string
    {
        if ($state) {
            $this->client->setState($state);
        }
        
        return $this->client->createAuthUrl();
    }

    /**
     * Handle authorization callback
     */
    public function handleCallback(string $code, int $userId = null): array
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                throw new Exception('OAuth2 Error: ' . ($token['error_description'] ?? $token['error']));
            }
            
            // Store tokens
            $this->storeTokens($token, $userId);
            
            Log::info('Google Drive OAuth2 tokens stored successfully', [
                'user_id' => $userId,
                'has_refresh_token' => isset($token['refresh_token'])
            ]);
            
            return [
                'success' => true,
                'token' => $token,
                'expires_at' => $this->getTokenExpirationTime($token)
            ];
            
        } catch (Exception $e) {
            Log::error('Google Drive OAuth2 callback failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            
            throw $e;
        }
    }

    /**
     * Get stored access token
     */
    public function getAccessToken(int $userId = null): ?array
    {
        $cacheKey = $this->getTokenCacheKey($userId);
        return Cache::get($cacheKey);
    }

    /**
     * Set access token
     */
    public function setAccessToken(array $token, int $userId = null): void
    {
        $this->client->setAccessToken($token);
        $this->storeTokens($token, $userId);
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(int $userId = null): bool
    {
        $token = $this->getAccessToken($userId);
        
        if (!$token) {
            return false;
        }
        
        $this->client->setAccessToken($token);
        
        // If token is expired, try to refresh it
        if ($this->client->isAccessTokenExpired()) {
            return $this->refreshAccessToken($userId);
        }
        
        return true;
    }

    /**
     * Refresh access token
     */
    public function refreshAccessToken(int $userId = null): bool
    {
        try {
            $token = $this->getAccessToken($userId);
            
            if (!$token || !isset($token['refresh_token'])) {
                Log::warning('No refresh token available for Google Drive', ['user_id' => $userId]);
                return false;
            }
            
            $this->client->setAccessToken($token);
            $refreshToken = $token['refresh_token'];
            
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            
            if (isset($newToken['error'])) {
                Log::error('Failed to refresh Google Drive token', [
                    'error' => $newToken['error_description'] ?? $newToken['error'],
                    'user_id' => $userId
                ]);
                return false;
            }
            
            // Preserve refresh token if not included in new token
            if (!isset($newToken['refresh_token']) && $refreshToken) {
                $newToken['refresh_token'] = $refreshToken;
            }
            
            $this->storeTokens($newToken, $userId);
            
            Log::info('Google Drive token refreshed successfully', ['user_id' => $userId]);
            
            return true;
            
        } catch (Exception $e) {
            Log::error('Failed to refresh Google Drive access token', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            
            return false;
        }
    }

    /**
     * Revoke access token
     */
    public function revokeToken(int $userId = null): bool
    {
        try {
            $token = $this->getAccessToken($userId);
            
            if ($token) {
                $this->client->setAccessToken($token);
                $this->client->revokeToken();
            }
            
            // Clear stored tokens
            $this->clearTokens($userId);
            
            Log::info('Google Drive token revoked successfully', ['user_id' => $userId]);
            
            return true;
            
        } catch (Exception $e) {
            Log::error('Failed to revoke Google Drive token', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            
            // Clear tokens anyway
            $this->clearTokens($userId);
            
            return false;
        }
    }

    /**
     * Get authenticated Google Client
     */
    public function getAuthenticatedClient(int $userId = null): ?Client
    {
        if (!$this->isAuthenticated($userId)) {
            return null;
        }
        
        return $this->client;
    }

    /**
     * Get token information
     */
    public function getTokenInfo(int $userId = null): ?array
    {
        $token = $this->getAccessToken($userId);
        
        if (!$token) {
            return null;
        }
        
        return [
            'access_token' => isset($token['access_token']) ? substr($token['access_token'], 0, 20) . '...' : null,
            'token_type' => $token['token_type'] ?? null,
            'expires_in' => $token['expires_in'] ?? null,
            'expires_at' => $this->getTokenExpirationTime($token),
            'has_refresh_token' => isset($token['refresh_token']),
            'scope' => $token['scope'] ?? null,
            'created' => $token['created'] ?? null
        ];
    }

    /**
     * Store tokens in cache
     */
    protected function storeTokens(array $token, int $userId = null): void
    {
        $cacheKey = $this->getTokenCacheKey($userId);
        $expiresAt = $this->getTokenExpirationTime($token);
        
        // Store with appropriate TTL
        $ttl = $expiresAt ? $expiresAt->diffInSeconds(now()) : $this->cacheTtl;
        
        Cache::put($cacheKey, $token, $ttl);
        
        // Also store in session for current user
        if (!$userId || $userId === auth()->id()) {
            Session::put('googledrive_token', $token);
        }
    }

    /**
     * Clear stored tokens
     */
    protected function clearTokens(int $userId = null): void
    {
        $cacheKey = $this->getTokenCacheKey($userId);
        Cache::forget($cacheKey);
        
        // Also clear from session
        if (!$userId || $userId === auth()->id()) {
            Session::forget('googledrive_token');
        }
    }

    /**
     * Get token cache key
     */
    protected function getTokenCacheKey(int $userId = null): string
    {
        $userId = $userId ?? auth()->id() ?? 'guest';
        return $this->cachePrefix . 'token_' . $userId;
    }

    /**
     * Get token expiration time
     */
    protected function getTokenExpirationTime(array $token): ?\Carbon\Carbon
    {
        if (isset($token['created']) && isset($token['expires_in'])) {
            return \Carbon\Carbon::createFromTimestamp($token['created'])->addSeconds($token['expires_in']);
        }
        
        if (isset($token['expires_in'])) {
            return now()->addSeconds($token['expires_in']);
        }
        
        return null;
    }

    /**
     * Get client instance
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Create instance for specific user
     */
    public static function forUser(int $userId): self
    {
        $instance = new self();
        $token = $instance->getAccessToken($userId);
        
        if ($token) {
            $instance->setAccessToken($token, $userId);
        }
        
        return $instance;
    }

    /**
     * Create instance for current authenticated user
     */
    public static function forCurrentUser(): self
    {
        return self::forUser(auth()->id());
    }
}