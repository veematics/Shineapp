<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\AppSettingCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\FeatureAccess;

class AppSettingController extends Controller
{
    protected $cacheService;
    
    public function __construct(AppSettingCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    /**
     * Display the app settings edit form.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        // Check permission
        if (!FeatureAccess::hasPermission('manage appsetting')) {
            abort(403, 'Unauthorized access to app settings.');
        }

        // Get app settings with appID = 1
        $appSetting = AppSetting::where('appID', 1)->first();
        
        // If no settings exist, create default ones
        if (!$appSetting) {
            $appSetting = AppSetting::create([
                'appID' => 1,
                'appName' => config('app.name', 'Laravel'),
                'appHeadline' => 'Welcome to our application',
                'appLogoBig' => '0',
                'appLogoSmall' => '0',
                'appLogoBigDark' => '0',
                'appLogoSmallDark' => '0',
                'appopenaikey' => '0',
                'appdeepseekkey' => '0',
                'appAITemperature' => 0.7,
                'appAiMaxToken' => 2000,
                'appAIDefaultModel' => []
            ]);
        }

        return view('admin.appsetting.edit', compact('appSetting'));
    }

    /**
     * Update the app settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Check permission
        if (!FeatureAccess::hasPermission('manage appsetting')) {
            abort(403, 'Unauthorized access to app settings.');
        }

        $validator = Validator::make($request->all(), [
            'appName' => 'required|string|max:255',
            'appHeadline' => 'nullable|string|max:500',
            'appLogoBig' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'appLogoSmall' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'appLogoBigDark' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'appLogoSmallDark' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'appopenaikey' => 'nullable|string|max:255',
            'appdeepseekkey' => 'nullable|string|max:255',
            'appAITemperature' => 'nullable|numeric|min:0|max:2',
            'appAiMaxToken' => 'nullable|integer|min:1|max:100000',
        ]);

        if ($validator->fails()) {
            // Get the active tab from request to preserve tab state after redirect
            $activeTab = $request->input('active_tab', 'globalsetting');
            $redirectUrl = route('admin.appsetting.edit') . '#' . $activeTab;
            
            return redirect($redirectUrl)
                ->withErrors($validator)
                ->withInput();
        }

        // Get or create app settings with appID = 1
        $appSetting = AppSetting::where('appID', 1)->first();
        
        if (!$appSetting) {
            $appSetting = new AppSetting();
            $appSetting->appID = 1;
        }

        // Update fields
        $appSetting->appName = $request->input('appName');
        $appSetting->appHeadline = $request->input('appHeadline', '');
        
        // Handle logo file uploads
        if ($request->hasFile('appLogoBig')) {
            // Delete old logo if exists
            if ($appSetting->appLogoBig && $appSetting->appLogoBig !== '0') {
                Storage::disk('public')->delete($appSetting->appLogoBig);
            }
            $appSetting->appLogoBig = $request->file('appLogoBig')->store('logos', 'public');
        }
        
        if ($request->hasFile('appLogoSmall')) {
            // Delete old logo if exists
            if ($appSetting->appLogoSmall && $appSetting->appLogoSmall !== '0') {
                Storage::disk('public')->delete($appSetting->appLogoSmall);
            }
            $appSetting->appLogoSmall = $request->file('appLogoSmall')->store('logos', 'public');
        }
        
        // Handle dark theme logo file uploads
        if ($request->hasFile('appLogoBigDark')) {
            // Delete old dark logo if exists
            if ($appSetting->appLogoBigDark && $appSetting->appLogoBigDark !== '0') {
                Storage::disk('public')->delete($appSetting->appLogoBigDark);
            }
            $appSetting->appLogoBigDark = $request->file('appLogoBigDark')->store('logos', 'public');
        }
        
        if ($request->hasFile('appLogoSmallDark')) {
            // Delete old dark logo if exists
            if ($appSetting->appLogoSmallDark && $appSetting->appLogoSmallDark !== '0') {
                Storage::disk('public')->delete($appSetting->appLogoSmallDark);
            }
            $appSetting->appLogoSmallDark = $request->file('appLogoSmallDark')->store('logos', 'public');
        }
        
        // Handle API keys with encryption
        $openaiKey = $request->input('appopenaikey', '0');
        $deepseekKey = $request->input('appdeepseekkey', '0');
        
        // Encrypt API keys if they are not empty or '0'
        if ($openaiKey && $openaiKey !== '0') {
            $appSetting->appopenaikey = Crypt::encryptString($openaiKey);
        } else {
            $appSetting->appopenaikey = '0';
        }
        
        if ($deepseekKey && $deepseekKey !== '0') {
            $appSetting->appdeepseekkey = Crypt::encryptString($deepseekKey);
        } else {
            $appSetting->appdeepseekkey = '0';
        }
        $appSetting->appAITemperature = $request->input('appAITemperature', 0.7);
        $appSetting->appAiMaxToken = $request->input('appAiMaxToken', 2000);
        
        // Handle clear models request
        if ($request->has('clear_models')) {
            $appSetting->appAIDefaultModel = [];
        } elseif ($request->has('appAIDefaultModel')) {
            // Handle AI models (if provided)
            $appSetting->appAIDefaultModel = $request->input('appAIDefaultModel', []);
        }
        
        // Handle fallback model setting
        if ($request->has('appAIFallbackModel')) {
            $fallbackModel = $request->input('appAIFallbackModel');
            if (!empty($fallbackModel) && $fallbackModel !== 'null') {
                // If it's already JSON, decode and re-encode to validate
                if (is_string($fallbackModel)) {
                    $decoded = json_decode($fallbackModel, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $appSetting->appAIFallbackModel = $decoded;
                    }
                } else {
                    $appSetting->appAIFallbackModel = $fallbackModel;
                }
            } else {
                $appSetting->appAIFallbackModel = null;
            }
        }

        $appSetting->save();
        
        // Refresh cache after successful update
        $this->cacheService->refreshCache();

        // Get the active tab from request to preserve tab state after redirect
        $activeTab = $request->input('active_tab', 'globalsetting');
        $redirectUrl = route('admin.appsetting.edit') . '#' . $activeTab;

        return redirect($redirectUrl)->with('success', 'App settings updated successfully!');
    }
    
    public function fetchModels(Request $request)
    {
        try {
            $openaiKey = $request->input('openai_key');
            $deepseekKey = $request->input('deepseek_key');
            $models = [];
            
            // Fetch OpenAI models
            if ($openaiKey) {
                try {
                    $openaiModels = $this->fetchOpenAIModels($openaiKey);
                    if ($openaiModels) {
                        $models['openai'] = $openaiModels;
                    }
                } catch (\Exception $e) {
                    \Log::error('OpenAI API Error: ' . $e->getMessage());
                }
            }
            
            // Fetch DeepSeek models
            if ($deepseekKey) {
                try {
                    $deepseekModels = $this->fetchDeepSeekModels($deepseekKey);
                    if ($deepseekModels) {
                        $models['deepseek'] = $deepseekModels;
                    }
                } catch (\Exception $e) {
                    \Log::error('DeepSeek API Error: ' . $e->getMessage());
                }
            }
            
            if (empty($models)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No models could be fetched. Please check your API keys.'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'models' => $models
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Fetch Models Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching models.'
            ]);
        }
    }
    
    private function fetchOpenAIModels($apiKey)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.openai.com/v1/models',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ]
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);
        
        if ($response === false || !empty($curlError)) {
            throw new \Exception('cURL Error: ' . $curlError);
        }
        
        if ($httpCode !== 200) {
            throw new \Exception('OpenAI API returned HTTP ' . $httpCode . '. Response: ' . $response);
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['data'])) {
            throw new \Exception('Invalid response from OpenAI API: ' . $response);
        }
        
        return $data['data'];
    }
    
    private function fetchDeepSeekModels($apiKey)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.deepseek.com/models',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ]
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);
        
        if ($response === false || !empty($curlError)) {
            throw new \Exception('cURL Error: ' . $curlError);
        }
        
        if ($httpCode !== 200) {
            throw new \Exception('DeepSeek API returned HTTP ' . $httpCode . '. Response: ' . $response);
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['data'])) {
            throw new \Exception('Invalid response from DeepSeek API: ' . $response);
        }
        
        return $data['data'];
    }
}