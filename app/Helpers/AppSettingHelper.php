<?php

namespace App\Helpers;

use App\Services\AppSettingCacheService;

class AppSettingHelper
{
    /**
     * Get app setting value from cache
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $cacheService = app(AppSettingCacheService::class);
        return $cacheService->getSetting($key, $default);
    }
    
    /**
     * Get all app settings from cache
     *
     * @return array|null
     */
    public static function all(): ?array
    {
        $cacheService = app(AppSettingCacheService::class);
        return $cacheService->getAppSettings();
    }
    
    /**
     * Refresh the app settings cache
     *
     * @return void
     */
    public static function refresh(): void
    {
        $cacheService = app(AppSettingCacheService::class);
        $cacheService->refreshCache();
    }
    
    /**
     * Clear the app settings cache
     *
     * @return void
     */
    public static function clear(): void
    {
        $cacheService = app(AppSettingCacheService::class);
        $cacheService->clearCache();
    }
    
    /**
     * Get a sensitive setting (retrieved directly from database, not cached)
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getSensitiveSetting(string $key, $default = null)
    {
        $cacheService = app(AppSettingCacheService::class);
        return $cacheService->getSensitiveSetting($key, $default);
    }
    
    /**
     * Get the fallback model from cache
     *
     * @return array|null
     */
    public static function getFallbackModel()
    {
        $cacheService = app(AppSettingCacheService::class);
        return $cacheService->getFallbackModel();
    }
    
    /**
     * Check if a fallback model is set
     *
     * @return bool
     */
    public static function hasFallbackModel(): bool
    {
        $cacheService = app(AppSettingCacheService::class);
        return $cacheService->hasFallbackModel();
    }
}