<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AppSetting;

echo "Final encryption test...\n";
echo "======================\n";

$setting = AppSetting::first();

if ($setting) {
    echo "Current state:\n";
    echo "Raw appopenaikey length: " . strlen($setting->getRawOriginal('appopenaikey')) . " characters\n";
    echo "Raw appdeepseekkey length: " . strlen($setting->getRawOriginal('appdeepseekkey')) . " characters\n";
    echo "Accessed appopenaikey: " . $setting->appopenaikey . "\n";
    echo "Accessed appdeepseekkey: " . $setting->appdeepseekkey . "\n\n";
    
    // Force encryption of both keys
    echo "Force encrypting both keys...\n";
    
    // Get current plain text values
    $openaiKey = $setting->appopenaikey;
    $deepseekKey = $setting->appdeepseekkey;
    
    // Force re-assignment to trigger mutators
    $setting->appopenaikey = $openaiKey;
    $setting->appdeepseekkey = $deepseekKey;
    
    // Save to database
    $setting->save();
    
    // Refresh model
    $setting->refresh();
    
    echo "\nAfter encryption:\n";
    $rawOpenAI = $setting->getRawOriginal('appopenaikey');
    $rawDeepSeek = $setting->getRawOriginal('appdeepseekkey');
    
    echo "Raw appopenaikey length: " . strlen($rawOpenAI) . " characters\n";
    echo "Raw appopenaikey preview: " . substr($rawOpenAI, 0, 50) . "...\n";
    echo "Accessed appopenaikey: " . $setting->appopenaikey . "\n";
    
    echo "Raw appdeepseekkey length: " . strlen($rawDeepSeek) . " characters\n";
    echo "Raw appdeepseekkey preview: " . substr($rawDeepSeek, 0, 50) . "...\n";
    echo "Accessed appdeepseekkey: " . $setting->appdeepseekkey . "\n";
    
    // Final encryption check
    $isOpenAIEncrypted = strlen($rawOpenAI) > 100 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawOpenAI);
    $isDeepSeekEncrypted = strlen($rawDeepSeek) > 100 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawDeepSeek);
    
    echo "\nFinal encryption status:\n";
    echo "appopenaikey encrypted: " . ($isOpenAIEncrypted ? 'YES' : 'NO') . "\n";
    echo "appdeepseekkey encrypted: " . ($isDeepSeekEncrypted ? 'YES' : 'NO') . "\n";
    
    if ($isOpenAIEncrypted && $isDeepSeekEncrypted) {
        echo "\n✅ Both API keys are now properly encrypted!\n";
    } else {
        echo "\n⚠️  Some keys may still need encryption.\n";
    }
    
} else {
    echo "No AppSetting record found.\n";
}

echo "\nDone.\n";