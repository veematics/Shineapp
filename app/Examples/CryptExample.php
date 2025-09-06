<?php

namespace App\Examples;

use Illuminate\Support\Facades\Crypt;

class CryptExample
{
    /**
     * Example of manual encryption control using Laravel's Crypt facade
     */
    public function encryptionExample()
    {
        // Encrypt a string
        $encrypted = Crypt::encryptString('sk-abc123');
        
        // Decrypt the string
        $decrypted = Crypt::decryptString($encrypted);
        
        return [
            'original' => 'sk-abc123',
            'encrypted' => $encrypted,
            'decrypted' => $decrypted
        ];
    }
    
    /**
     * Example of encrypting API keys or sensitive data
     */
    public function encryptApiKey($apiKey)
    {
        if (empty($apiKey)) {
            return null;
        }
        
        return Crypt::encryptString($apiKey);
    }
    
    /**
     * Example of decrypting API keys or sensitive data
     */
    public function decryptApiKey($encryptedKey)
    {
        if (empty($encryptedKey)) {
            return null;
        }
        
        try {
            return Crypt::decryptString($encryptedKey);
        } catch (\Exception $e) {
            \Log::warning('Failed to decrypt API key: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Example of checking if a value is encrypted
     */
    public function isEncrypted($value)
    {
        if (empty($value) || strlen($value) < 20) {
            return false;
        }
        
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}