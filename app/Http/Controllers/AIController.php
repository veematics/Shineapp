<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class AIController extends Controller
{
    /**
     * Test OpenAI API connection
     */
    public function testOpenAI(Request $request): JsonResponse
    {
        try {
            $ai = AIService::openai();
            
            $response = $ai->complete(
                $request->input('prompt', 'Hello, how are you?')
            );

            return response()->json([
                'success' => true,
                'service' => 'OpenAI',
                'response' => $response,
                'models' => $ai->getModels()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test DeepSeek API connection
     */
    public function testDeepSeek(Request $request): JsonResponse
    {
        try {
            $ai = AIService::deepseek();
            
            $response = $ai->complete(
                $request->input('prompt', 'Hello, how are you?')
            );

            return response()->json([
                'success' => true,
                'service' => 'DeepSeek',
                'response' => $response,
                'models' => $ai->getModels()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chat with AI using the default service
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'messages' => 'required|array',
            'service' => 'sometimes|string|in:openai,deepseek'
        ]);

        try {
            $service = $request->input('service');
            $ai = $service ? AIService::for($service) : new AIService();
            
            $response = $ai->chat($request->input('messages'));

            return response()->json([
                'success' => true,
                'service' => $ai->getService(),
                'response' => $response
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available AI services and their models
     */
    public function services(): JsonResponse
    {
        try {
            $services = [];
            
            // Check OpenAI
            try {
                $openai = AIService::openai();
                $services['openai'] = [
                    'name' => 'OpenAI',
                    'models' => $openai->getModels(),
                    'available' => true
                ];
            } catch (Exception $e) {
                $services['openai'] = [
                    'name' => 'OpenAI',
                    'available' => false,
                    'error' => $e->getMessage()
                ];
            }

            // Check DeepSeek
            try {
                $deepseek = AIService::deepseek();
                $services['deepseek'] = [
                    'name' => 'DeepSeek',
                    'models' => $deepseek->getModels(),
                    'available' => true
                ];
            } catch (Exception $e) {
                $services['deepseek'] = [
                    'name' => 'DeepSeek',
                    'available' => false,
                    'error' => $e->getMessage()
                ];
            }

            return response()->json([
                'success' => true,
                'services' => $services,
                'default' => config('ai.default')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}