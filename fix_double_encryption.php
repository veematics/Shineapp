<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AppSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

echo "Fixing double encryption issue...\n";
echo "==================================\n";

$setting = AppSetting::first();

if ($setting) {
    // Get raw values from database (these are currently encrypted)
    $rawOpenAI = $setting->getRawOriginal('appopenaikey');
    $rawDeepSeek = $setting->getRawOriginal('appdeepseekkey');
    
    echo "Current raw appopenaikey length: " . strlen($rawOpenAI) . " characters\n";
    echo "Current raw appdeepseekkey length: " . strlen($rawDeepSeek) . " characters\n\n";
    
    try {
        // Try to decrypt the current encrypted values to get the original plain text
        $decryptedOpenAI = Crypt::decrypt($rawOpenAI);
        $decryptedDeepSeek = Crypt::decrypt($rawDeepSeek);
        
        echo "Successfully decrypted current values:\n";
        echo "OpenAI Key: " . $decryptedOpenAI . "\n";
        echo "DeepSeek Key: " . $decryptedDeepSeek . "\n\n";
        
        // Now we have the original plain text values
        // Let's update the database with these plain text values
        // The model's encrypted cast will handle the encryption automatically
        
        echo "Updating with plain text values (model will auto-encrypt)...\n";
        
        // Update directly in database with plain text
        DB::table('appsetting')
            ->where('appID', $setting->appID)
            ->update([
                'appopenaikey' => $decryptedOpenAI,
                'appdeepseekkey' => $decryptedDeepSeek
            ]);
        
        echo "Database updated with plain text values.\n\n";
        
        // Now verify that the model's encrypted cast works correctly
        echo "Verification - Model should auto-encrypt/decrypt:\n";
        $freshSetting = AppSetting::first();
        
        echo "Raw appopenaikey from DB: " . substr($freshSetting->getRawOriginal('appopenaikey'), 0, 50) . "...\n";
        echo "Accessed appopenaikey (should be plain): " . $freshSetting->appopenaikey . "\n";
        echo "Raw appdeepseekkey from DB: " . substr($freshSetting->getRawOriginal('appdeepseekkey'), 0, 50) . "...\n";
        echo "Accessed appdeepseekkey (should be plain): " . $freshSetting->appdeepseekkey . "\n\n";
        
        echo "Double encryption issue fixed!\n";
        
    } catch (Exception $e) {
        echo "Error decrypting current values: " . $e->getMessage() . "\n";
        echo "The values might already be in the correct format.\n";
    }
} else {
    echo "No AppSetting record found.\n";
}

echo "\nDone.\n";