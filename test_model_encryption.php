<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AppSetting;

echo "Testing model encryption...\n";
echo "==========================\n";

$setting = AppSetting::first();

if ($setting) {
    echo "Current values:\n";
    echo "Raw appopenaikey: " . substr($setting->getRawOriginal('appopenaikey'), 0, 50) . "...\n";
    echo "Accessed appopenaikey: " . $setting->appopenaikey . "\n";
    echo "Raw appdeepseekkey: " . $setting->getRawOriginal('appdeepseekkey') . "\n";
    echo "Accessed appdeepseekkey: " . $setting->appdeepseekkey . "\n\n";
    
    // Test saving through the model to trigger encryption
    echo "Saving through model to trigger encryption...\n";
    $setting->save();
    
    // Refresh from database
    $setting->refresh();
    
    echo "\nAfter model save:\n";
    echo "Raw appopenaikey: " . substr($setting->getRawOriginal('appopenaikey'), 0, 50) . "...\n";
    echo "Accessed appopenaikey: " . $setting->appopenaikey . "\n";
    echo "Raw appdeepseekkey: " . $setting->getRawOriginal('appdeepseekkey') . "\n";
    echo "Accessed appdeepseekkey: " . $setting->appdeepseekkey . "\n";
    
    // Check if values are now encrypted
    $rawOpenAI = $setting->getRawOriginal('appopenaikey');
    $rawDeepSeek = $setting->getRawOriginal('appdeepseekkey');
    
    $isOpenAIEncrypted = strlen($rawOpenAI) > 100 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawOpenAI);
    $isDeepSeekEncrypted = strlen($rawDeepSeek) > 50 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawDeepSeek);
    
    echo "\nEncryption status:\n";
    echo "appopenaikey encrypted: " . ($isOpenAIEncrypted ? 'YES' : 'NO') . "\n";
    echo "appdeepseekkey encrypted: " . ($isDeepSeekEncrypted ? 'YES' : 'NO') . "\n";
    
} else {
    echo "No AppSetting record found.\n";
}

echo "\nDone.\n";