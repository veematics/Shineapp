<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AppSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

echo "Encrypting existing API keys...\n";
echo "================================\n";

$setting = AppSetting::first();

if ($setting) {
    $rawOpenAI = $setting->getRawOriginal('appopenaikey');
    $rawDeepSeek = $setting->getRawOriginal('appdeepseekkey');
    
    echo "Current appopenaikey (raw): " . $rawOpenAI . "\n";
    echo "Current appdeepseekkey (raw): " . $rawDeepSeek . "\n\n";
    
    // Check if already encrypted (encrypted values are typically much longer and base64-like)
    $isOpenAIEncrypted = strlen($rawOpenAI) > 100 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawOpenAI);
    $isDeepSeekEncrypted = strlen($rawDeepSeek) > 100 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $rawDeepSeek);
    
    if (!$isOpenAIEncrypted || !$isDeepSeekEncrypted) {
        echo "Encrypting keys...\n";
        
        $updates = [];
        
        if (!$isOpenAIEncrypted && $rawOpenAI && $rawOpenAI !== '0') {
            $encryptedOpenAI = Crypt::encrypt($rawOpenAI);
            $updates['appopenaikey'] = $encryptedOpenAI;
            echo "OpenAI key will be encrypted\n";
        }
        
        if (!$isDeepSeekEncrypted && $rawDeepSeek && $rawDeepSeek !== '0') {
            $encryptedDeepSeek = Crypt::encrypt($rawDeepSeek);
            $updates['appdeepseekkey'] = $encryptedDeepSeek;
            echo "DeepSeek key will be encrypted\n";
        }
        
        if (!empty($updates)) {
            // Update directly in database to bypass model casts
            DB::table('appsetting')
                ->where('appID', $setting->appID)
                ->update($updates);
            
            echo "\nKeys updated in database.\n";
            
            // Verify encryption worked
            echo "\nVerification after encryption:\n";
            $freshSetting = AppSetting::first();
            echo "Raw appopenaikey length: " . strlen($freshSetting->getRawOriginal('appopenaikey')) . " characters\n";
            echo "Decrypted appopenaikey: " . $freshSetting->appopenaikey . "\n";
            echo "Raw appdeepseekkey length: " . strlen($freshSetting->getRawOriginal('appdeepseekkey')) . " characters\n";
            echo "Decrypted appdeepseekkey: " . $freshSetting->appdeepseekkey . "\n";
            
            echo "\nKeys successfully encrypted!\n";
        } else {
            echo "No keys to encrypt.\n";
        }
    } else {
        echo "Keys are already encrypted.\n";
    }
} else {
    echo "No AppSetting record found.\n";
}

echo "\nDone.\n";