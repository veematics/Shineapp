<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AppSetting;

echo "Force encryption by setting attributes...\n";
echo "======================================\n";

$setting = AppSetting::first();

if ($setting) {
    echo "Current values:\n";
    echo "Raw appopenaikey: " . substr($setting->getRawOriginal('appopenaikey'), 0, 50) . "...\n";
    echo "Raw appdeepseekkey: " . $setting->getRawOriginal('appdeepseekkey') . "\n\n";
    
    // Get the current plain text values
    $currentOpenAI = $setting->appopenaikey;
    $currentDeepSeek = $setting->appdeepseekkey;
    
    echo "Current accessed values:\n";
    echo "OpenAI: " . $currentOpenAI . "\n";
    echo "DeepSeek: " . $currentDeepSeek . "\n\n";
    
    // Force re-assignment to trigger encryption
    echo "Re-assigning values to trigger encryption...\n";
    $setting->appopenaikey = $currentOpenAI;
    $setting->appdeepseekkey = $currentDeepSeek;
    
    // Save the model
    $setting->save();
    
    // Refresh from database
    $setting->refresh();
    
    echo "\nAfter re-assignment and save:\n";
    $rawOpenAI = $setting->getRawOriginal('appopenaikey');
    $rawDeepSeek = $setting->getRawOriginal('appdeepseekkey');
    
    echo "Raw appopenaikey length: " . strlen($rawOpenAI) . " characters\n";
    echo "Raw appopenaikey preview: " . substr($rawOpenAI, 0, 50) . "...\n";
    echo "Accessed appopenaikey: " . $setting->appopenaikey . "\n";
    echo "Raw appdeepseekkey length: " . strlen($rawDeepSeek) . " characters\n";
    echo "Raw appdeepseekkey preview: " . substr($rawDeepSeek, 0, 50) . "...\n";
    echo "Accessed appdeepseekkey: " . $setting->appdeepseekkey . "\n";
    
    // Check encryption status
    $isOpenAIEncrypted = strlen($rawOpenAI) > 100 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawOpenAI);
    $isDeepSeekEncrypted = strlen($rawDeepSeek) > 50 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawDeepSeek);
    
    echo "\nEncryption status:\n";
    echo "appopenaikey encrypted: " . ($isOpenAIEncrypted ? 'YES' : 'NO') . "\n";
    echo "appdeepseekkey encrypted: " . ($isDeepSeekEncrypted ? 'YES' : 'NO') . "\n";
    
    if ($isOpenAIEncrypted && $isDeepSeekEncrypted) {
        echo "\n✅ Encryption successful!\n";
    } else {
        echo "\n❌ Encryption failed.\n";
    }
    
} else {
    echo "No AppSetting record found.\n";
}

echo "\nDone.\n";