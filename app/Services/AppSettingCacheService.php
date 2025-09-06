<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AppSettingCacheService
{
    /**
     * Get the cache key for app settings
     */
    private function getCacheKey(): string
    {
        return config('app.cache_key', 'dxshine') . '_app_settings';
    }

    /**
     * Get app settings from cache or database
     */
    public function getAppSettings(): ?array
    {
        $cacheKey = $this->getCacheKey();
        
        // Try to get from cache first
        $cachedSettings = Cache::get($cacheKey);
        
        if ($cachedSettings) {
            Log::info('App settings retrieved from cache');
            return json_decode($cachedSettings, true);
        }
        
        // If not in cache, get from database
        $appSetting = AppSetting::first();
        
        if ($appSetting) {
            $settingsArray = $this->prepareSettingsForCache($appSetting);
            $this->cacheAppSettings($settingsArray);
            Log::info('App settings retrieved from database and cached');
            return $settingsArray;
        }
        
        Log::warning('No app settings found in database');
        return null;
    }
    
    /**
     * Cache app settings in JSON format
     */
    public function cacheAppSettings(array $settings): void
    {
        $cacheKey = $this->getCacheKey();
        $jsonSettings = json_encode($settings);
        
        // Cache for 24 hours (1440 minutes)
        Cache::put($cacheKey, $jsonSettings, 1440);
        
        Log::info('App settings cached successfully', ['cache_key' => $cacheKey]);
    }
    
    /**
     * Refresh cache with latest app settings from database
     */
    public function refreshCache(): void
    {
        $cacheKey = $this->getCacheKey();
        
        // Clear existing cache
        Cache::forget($cacheKey);
        
        // Get fresh data from database
        $appSetting = AppSetting::first();
        
        if ($appSetting) {
            $settingsArray = $this->prepareSettingsForCache($appSetting);
            $this->cacheAppSettings($settingsArray);
            Log::info('App settings cache refreshed successfully');
        } else {
            Log::warning('Cannot refresh cache: No app settings found in database');
        }
    }
    
    /**
     * Clear app settings cache
     */
    public function clearCache(): void
    {
        $cacheKey = $this->getCacheKey();
        Cache::forget($cacheKey);
        Log::info('App settings cache cleared');
    }
    
    /**
     * Get a specific setting value from cache
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->getAppSettings();
        
        if ($settings && array_key_exists($key, $settings)) {
            return $settings[$key];
        }
        
        return $default;
    }
    
    /**
     * Prepare settings for caching, excluding sensitive fields
     */
    private function prepareSettingsForCache(AppSetting $appSetting): array
    {
        $settingsArray = $appSetting->toArray();
        
        // Remove sensitive fields from cache for security
        // These will be retrieved directly from database when needed
        unset($settingsArray['appopenaikey']);
        unset($settingsArray['appdeepseekkey']);
        
        return $settingsArray;
    }
    
    /**
     * Get a sensitive setting directly from database (not cached)
     */
    public function getSensitiveSetting(string $key, $default = null)
    {
        $sensitiveFields = ['appopenaikey', 'appdeepseekkey'];
        
        if (!in_array($key, $sensitiveFields)) {
            // Not a sensitive field, use regular cache method
            return $this->getSetting($key, $default);
        }
        
        // Get sensitive field directly from database
        $appSetting = AppSetting::first();
        
        if ($appSetting && isset($appSetting->$key)) {
            return $appSetting->$key;
        }
        
        return $default;
    }
    
    /**
     * Get the fallback model from cache
     */
    public function getFallbackModel()
    {
        return $this->getSetting('appAIFallbackModel', null);
    }
    
    /**
     * Check if a fallback model is set
     */
    public function hasFallbackModel(): bool
    {
        $fallbackModel = $this->getFallbackModel();
        return !empty($fallbackModel) && is_array($fallbackModel) && isset($fallbackModel['provider']) && isset($fallbackModel['model']);
    }
}