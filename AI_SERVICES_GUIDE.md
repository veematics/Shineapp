# AI Services Integration Guide

This Laravel application now supports both OpenAI and DeepSeek APIs through a unified service interface.

## Installation

The required packages have been installed:
- `openai-php/client` - Official OpenAI PHP client (compatible with DeepSeek)

## Configuration

### Environment Variables

Add the following variables to your `.env` file:

```env
# AI Services Configuration
AI_DEFAULT_SERVICE=openai

# OpenAI API Configuration
OPENAI_API_KEY=your_openai_api_key_here
OPENAI_ORGANIZATION=your_openai_organization_id_here

# DeepSeek API Configuration
DEEPSEEK_API_KEY=your_deepseek_api_key_here
```

### Configuration File

The AI services are configured in `config/ai.php` with:
- API endpoints for both services
- Available models
- Request timeouts and defaults

## Usage

### Basic Usage

```php
use App\Services\AIService;

// Use default service (configured in AI_DEFAULT_SERVICE)
$ai = new AIService();
$response = $ai->complete('Hello, how are you?');

// Use specific service
$openai = AIService::openai();
$deepseek = AIService::deepseek();

// Chat completion
$messages = [
    ['role' => 'user', 'content' => 'Hello!']
];
$response = $ai->chat($messages);
```

### Available Methods

- `complete(string $prompt, array $options = [])` - Simple text completion
- `chat(array $messages, array $options = [])` - Chat completion with message history
- `getModels()` - Get available models for the service
- `getService()` - Get current service name

### Static Methods

- `AIService::openai()` - Create OpenAI instance
- `AIService::deepseek()` - Create DeepSeek instance
- `AIService::for(string $service)` - Create instance for specific service

## API Routes

The following routes are available under `/admin/ai/` (requires `manage appsetting` permission):

- `GET /admin/ai/services` - List available services and their status
- `POST /admin/ai/chat` - Chat with AI (specify service in request)
- `POST /admin/ai/test-openai` - Test OpenAI connection
- `POST /admin/ai/test-deepseek` - Test DeepSeek connection

### Example API Requests

#### Test OpenAI
```bash
curl -X POST /admin/ai/test-openai \
  -H "Content-Type: application/json" \
  -d '{"prompt": "Hello, world!"}'
```

#### Chat Request
```bash
curl -X POST /admin/ai/chat \
  -H "Content-Type: application/json" \
  -d '{
    "service": "deepseek",
    "messages": [
      {"role": "user", "content": "Hello!"}
    ]
  }'
```

## Service Compatibility

Both OpenAI and DeepSeek use the same API format, so the same client library works for both:

### OpenAI Models
- gpt-4
- gpt-4-turbo-preview
- gpt-3.5-turbo

### DeepSeek Models
- deepseek-chat
- deepseek-coder

## Error Handling

The service includes proper error handling:
- Missing API keys
- Invalid service configuration
- API request failures
- Network timeouts

## Security

- All AI routes are protected with authentication
- Requires `manage appsetting` permission
- API keys are stored in environment variables
- Never expose API keys in responses

## Getting API Keys

### OpenAI
1. Visit [OpenAI Platform](https://platform.openai.com/)
2. Create an account and navigate to API keys
3. Generate a new API key

### DeepSeek
1. Visit [DeepSeek Platform](https://platform.deepseek.com/)
2. Create an account and navigate to API keys
3. Generate a new API key

## Example Integration

```php
// In a controller
public function generateContent(Request $request)
{
    try {
        $ai = AIService::deepseek(); // or AIService::openai()
        
        $response = $ai->complete($request->input('prompt'));
        
        return response()->json([
            'success' => true,
            'content' => $response
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
```