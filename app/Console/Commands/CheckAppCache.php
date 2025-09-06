<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\AppSettingHelper;
use App\Services\AppSettingCacheService;
use Illuminate\Support\Facades\Cache;

class CheckAppCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check current app cache contents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== App Settings Cache Contents ===');
        
        // Get cache key
        $cacheKey = config('app.cache_key', 'dxshine') . '_app_settings';
        $this->info("Cache Key: {$cacheKey}");
        
        // Check if cache exists
        if (Cache::has($cacheKey)) {
            $this->info('✓ Cache exists');
            
            // Get all settings from cache
            $settings = AppSettingHelper::all();
            if ($settings) {
                $this->info('\n--- Cached Settings ---');
                foreach ($settings as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        $this->line("{$key}: " . json_encode($value));
                    } else {
                        $this->line("{$key}: {$value}");
                    }
                }
            }
            
            // Check fallback model specifically
            $this->info('\n--- Fallback Model ---');
            if (AppSettingHelper::hasFallbackModel()) {
                $fallback = AppSettingHelper::getFallbackModel();
                $this->info('✓ Fallback model is set:');
                $this->line(json_encode($fallback, JSON_PRETTY_PRINT));
            } else {
                $this->warn('✗ No fallback model set');
            }
            
        } else {
            $this->warn('✗ No cache found');
            $this->info('Cache will be created on first access.');
        }
        
        // Show cache driver
        $this->info('\n--- Cache Configuration ---');
        $this->line('Cache Driver: ' . config('cache.default'));
        
        return 0;
    }
}