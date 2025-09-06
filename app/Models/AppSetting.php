<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AppSetting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'appsetting';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'appID';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'appName',
        'appHeadline',
        'appLogoBig',
        'appLogoSmall',
        'appLogoBigDark',
        'appLogoSmallDark',
        'appopenaikey',
        'appdeepseekkey',
        'appAITemperature',
        'appAiMaxToken',
        'appAIDefaultModel',
        'appAIFallbackModel',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appAITemperature' => 'float',
        'appAiMaxToken' => 'integer',
        'appAIDefaultModel' => 'array',
        'appAIFallbackModel' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'appopenaikey',
        'appdeepseekkey',
    ];

    /**
     * Get the application settings (singleton pattern)
     *
     * @return AppSetting|null
     */
    public static function getSettings(): ?AppSetting
    {
        return static::first();
    }
    
    /**
     * Override getAttribute to ensure accessors are called for sensitive fields
     */
    public function getAttribute($key)
    {
        $sensitiveFields = ['appopenaikey', 'appdeepseekkey'];
        
        if (in_array($key, $sensitiveFields)) {
            // Get the raw value from database
            $rawValue = $this->getRawOriginal($key);
            
            // Call the appropriate accessor method directly
            if ($key === 'appopenaikey') {
                return $this->getAppopenaikeyAttribute($rawValue);
            } elseif ($key === 'appdeepseekkey') {
                return $this->getAppdeepseekeyAttribute($rawValue);
            }
        }
        
        return parent::getAttribute($key);
    }

    /**
     * Decrypt the OpenAI API key when accessing
     */
    public function getAppopenaikeyAttribute($value)
    {
        if ($value && $value !== '0') {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                \Log::warning('Failed to decrypt appopenaikey: ' . $e->getMessage());
                return $value;
            }
        }
        return $value;
    }
    
    /**
     * Decrypt the DeepSeek API key when accessing
     */
    public function getAppdeepseekeyAttribute($value)
    {
        if ($value && $value !== '0') {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                \Log::warning('Failed to decrypt appdeepseekkey: ' . $e->getMessage());
                return $value;
            }
        }
        return $value;
    }

    /**
     * Get OpenAI API key
     *
     * @return string|null
     */
    public function getOpenAIKey(): ?string
    {
        return $this->appopenaikey !== '0' ? $this->appopenaikey : null;
    }

    /**
     * Get DeepSeek API key
     *
     * @return string|null
     */
    public function getDeepSeekKey(): ?string
    {
        return $this->appdeepseekkey !== '0' ? $this->appdeepseekkey : null;
    }

    /**
     * Get AI temperature setting
     *
     * @return float
     */
    public function getAITemperature(): float
    {
        return $this->appAITemperature ?? 0.7;
    }

    /**
     * Get AI max tokens setting
     *
     * @return int
     */
    public function getAIMaxTokens(): int
    {
        return $this->appAiMaxToken ?? 2000;
    }

    /**
     * Get AI models configuration
     *
     * @return array
     */
    public function getAIModels(): array
    {
        return $this->appAIDefaultModel ?? [];
    }

    /**
     * Get models for specific service
     *
     * @param string $service
     * @return array
     */
    public function getModelsForService(string $service): array
    {
        $models = $this->getAIModels();
        return $models[$service]['models'] ?? [];
    }

    /**
     * Get default model for service
     *
     * @param string $service
     * @return string|null
     */
    public function getDefaultModelForService(string $service): ?string
    {
        $models = $this->getModelsForService($service);
        return reset($models) ?: null;
    }

    /**
     * Get app logo (big)
     *
     * @return string|null
     */
    public function getLogoBig(): ?string
    {
        return $this->appLogoBig !== '0' ? $this->appLogoBig : null;
    }

    /**
     * Get app logo (small)
     *
     * @return string|null
     */
    public function getLogoSmall(): ?string
    {
        return $this->appLogoSmall !== '0' ? $this->appLogoSmall : null;
    }

    /**
     * Get AI default models
     *
     * @return array
     */
    public function getAIDefaultModels(): array
    {
        return $this->appAIDefaultModel ?? [];
    }
}