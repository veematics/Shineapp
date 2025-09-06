<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AppSetting;
use Illuminate\Support\Facades\DB;

echo "Force DeepSeek key encryption...\n";
echo "===============================\n";

$setting = AppSetting::first();

if ($setting) {
    $deepseekKey = 'sk-998a5525cd114eeb8c5ea2217fb061ab';
    
    echo "Current DeepSeek key: " . $setting->appdeepseekkey . "\n";
    echo "Current raw length: " . strlen($setting->getRawOriginal('appdeepseekkey')) . "\n\n";
    
    // First, set to a different value to force a change
    echo "Step 1: Setting to temporary value...\n";
    $setting->appdeepseekkey = 'temp_value';
    $setting->save();
    
    echo "Step 2: Setting to actual DeepSeek key...\n";
    $setting->appdeepseekkey = $deepseekKey;
    $setting->save();
    
    // Refresh and check
    $setting->refresh();
    
    echo "\nAfter forced encryption:\n";
    $rawValue = $setting->getRawOriginal('appdeepseekkey');
    echo "Raw value length: " . strlen($rawValue) . "\n";
    echo "Raw value preview: " . substr($rawValue, 0, 50) . "...\n";
    echo "Accessed value: " . $setting->appdeepseekkey . "\n";
    
    // Check if encrypted
    $isEncrypted = strlen($rawValue) > 100 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawValue);
    echo "Is encrypted: " . ($isEncrypted ? 'YES' : 'NO') . "\n";
    
    if (!$isEncrypted) {
        echo "\nManual encryption fallback...\n";
        // Manually encrypt and update database
        $encryptedValue = encrypt($deepseekKey);
        DB::table('appsetting')
            ->where('appID', $setting->appID)
            ->update(['appdeepseekkey' => $encryptedValue]);
        
        // Verify manual encryption
        $setting->refresh();
        $rawValue = $setting->getRawOriginal('appdeepseekkey');
        echo "After manual encryption:\n";
        echo "Raw value length: " . strlen($rawValue) . "\n";
        echo "Accessed value: " . $setting->appdeepseekkey . "\n";
        
        $isEncrypted = strlen($rawValue) > 100 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawValue);
        echo "Is encrypted: " . ($isEncrypted ? 'YES' : 'NO') . "\n";
    }
    
} else {
    echo "No AppSetting record found.\n";
}

echo "\nDone.\n";