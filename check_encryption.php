<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AppSetting;

echo "Checking AppSetting encryption status:\n";
echo "=====================================\n";

$setting = AppSetting::first();

if ($setting) {
    echo "Raw appopenaikey from DB: " . $setting->getRawOriginal('appopenaikey') . "\n";
    echo "Accessed appopenaikey: " . $setting->appopenaikey . "\n";
    echo "Raw appdeepseekkey from DB: " . $setting->getRawOriginal('appdeepseekkey') . "\n";
    echo "Accessed appdeepseekkey: " . $setting->appdeepseekkey . "\n";
    
    // Check if the raw values look encrypted (base64 encoded)
    $rawOpenAI = $setting->getRawOriginal('appopenaikey');
    $rawDeepSeek = $setting->getRawOriginal('appdeepseekkey');
    
    echo "\nEncryption Analysis:\n";
    echo "appopenaikey appears encrypted: " . (preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawOpenAI) && strlen($rawOpenAI) > 50 ? 'YES' : 'NO') . "\n";
    echo "appdeepseekkey appears encrypted: " . (preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawDeepSeek) && strlen($rawDeepSeek) > 50 ? 'YES' : 'NO') . "\n";
} else {
    echo "No AppSetting record found in database.\n";
}

echo "\nDone.\n";