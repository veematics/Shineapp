<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AppSetting;

echo "Debug encryption process...\n";
echo "==========================\n";

$setting = AppSetting::first();

if ($setting) {
    $deepseekKey = 'sk-998a5525cd114eeb8c5ea2217fb061ab';
    
    echo "Testing DeepSeek key: " . $deepseekKey . "\n";
    echo "Key length: " . strlen($deepseekKey) . " characters\n";
    
    // Test encryption directly
    echo "\nTesting direct encryption:\n";
    $encrypted = encrypt($deepseekKey);
    echo "Encrypted length: " . strlen($encrypted) . " characters\n";
    echo "Encrypted preview: " . substr($encrypted, 0, 50) . "...\n";
    
    // Test decryption
    $decrypted = decrypt($encrypted);
    echo "Decrypted: " . $decrypted . "\n";
    echo "Match original: " . ($decrypted === $deepseekKey ? 'YES' : 'NO') . "\n";
    
    // Now test with model
    echo "\nTesting with model mutator:\n";
    $setting->appdeepseekkey = $deepseekKey;
    echo "After setting attribute (before save):\n";
    echo "Raw value: " . $setting->getRawOriginal('appdeepseekkey') . "\n";
    echo "Accessed value: " . $setting->appdeepseekkey . "\n";
    
    $setting->save();
    $setting->refresh();
    
    echo "\nAfter save and refresh:\n";
    echo "Raw value length: " . strlen($setting->getRawOriginal('appdeepseekkey')) . "\n";
    echo "Raw value preview: " . substr($setting->getRawOriginal('appdeepseekkey'), 0, 50) . "...\n";
    echo "Accessed value: " . $setting->appdeepseekkey . "\n";
    
} else {
    echo "No AppSetting record found.\n";
}

echo "\nDone.\n";