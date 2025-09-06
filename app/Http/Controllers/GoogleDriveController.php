<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class GoogleDriveController extends Controller
{
    protected GoogleDriveService $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    /**
     * Display Google Drive dashboard
     */
    public function index(): View
    {
        $isAuthenticated = $this->googleDriveService->isAuthenticated();
        $authUrl = $isAuthenticated ? null : $this->googleDriveService->getAuthUrl();
        
        return view('admin.googledrive.index', compact('isAuthenticated', 'authUrl'));
    }

    /**
     * Handle OAuth2 authentication
     */
    public function authenticate(): RedirectResponse
    {
        $authUrl = $this->googleDriveService->getAuthUrl();
        return redirect($authUrl);
    }

    /**
     * Handle OAuth2 callback
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            $code = $request->get('code');
            
            if (!$code) {
                return redirect()->route('admin.googledrive.index')
                    ->with('error', 'Authorization code not received.');
            }
            
            $this->googleDriveService->handleCallback($code);
            
            return redirect()->route('admin.googledrive.index')
                ->with('success', 'Successfully connected to Google Drive!');
                
        } catch (Exception $e) {
            Log::error('Google Drive OAuth callback failed', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.googledrive.index')
                ->with('error', 'Failed to connect to Google Drive: ' . $e->getMessage());
        }
    }

    /**
     * List files via API
     */
    public function listFiles(Request $request): JsonResponse
    {
        try {
            if (!$this->googleDriveService->isAuthenticated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated with Google Drive'
                ], 401);
            }
            
            $folderId = $request->get('folder_id');
            $pageSize = min($request->get('page_size', 10), 100);
            $pageToken = $request->get('page_token');
            
            $result = $this->googleDriveService->listFiles($folderId, $pageSize, $pageToken);
            
            return response()->json($result);
            
        } catch (Exception $e) {
            Log::error('Google Drive list files failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to list files: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload file
     */
    public function uploadFile(Request $request): JsonResponse
    {
        try {
            if (!$this->googleDriveService->isAuthenticated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated with Google Drive'
                ], 401);
            }
            
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:' . (config('googledrive.max_file_size') / 1024),
                'folder_id' => 'nullable|string',
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $file = $request->file('file');
            $folderId = $request->get('folder_id');
            $metadata = [
                'name' => $request->get('name'),
                'description' => $request->get('description')
            ];
            
            $result = $this->googleDriveService->uploadFile($file, $folderId, $metadata);
            
            return response()->json($result);
            
        } catch (Exception $e) {
            Log::error('Google Drive upload failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download file
     */
    public function downloadFile(Request $request, string $fileId)
    {
        try {
            if (!$this->googleDriveService->isAuthenticated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated with Google Drive'
                ], 401);
            }
            
            $result = $this->googleDriveService->downloadFile($fileId);
            
            if ($result['success']) {
                $file = $result['file'];
                
                return response($file['content'])
                    ->header('Content-Type', $file['mimeType'])
                    ->header('Content-Disposition', 'attachment; filename="' . $file['name'] . '"')
                    ->header('Content-Length', $file['size']);
            }
            
            return response()->json($result, 500);
            
        } catch (Exception $e) {
            Log::error('Google Drive download failed', ['file_id' => $fileId, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to download file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file
     */
    public function deleteFile(Request $request, string $fileId): JsonResponse
    {
        try {
            if (!$this->googleDriveService->isAuthenticated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated with Google Drive'
                ], 401);
            }
            
            $result = $this->googleDriveService->deleteFile($fileId);
            
            return response()->json($result);
            
        } catch (Exception $e) {
            Log::error('Google Drive delete failed', ['file_id' => $fileId, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create folder
     */
    public function createFolder(Request $request): JsonResponse
    {
        try {
            if (!$this->googleDriveService->isAuthenticated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated with Google Drive'
                ], 401);
            }
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $name = $request->get('name');
            $parentId = $request->get('parent_id');
            
            $result = $this->googleDriveService->createFolder($name, $parentId);
            
            return response()->json($result);
            
        } catch (Exception $e) {
            Log::error('Google Drive create folder failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create folder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file information
     */
    public function getFileInfo(Request $request, string $fileId): JsonResponse
    {
        try {
            if (!$this->googleDriveService->isAuthenticated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated with Google Drive'
                ], 401);
            }
            
            $result = $this->googleDriveService->getFileInfo($fileId);
            
            return response()->json($result);
            
        } catch (Exception $e) {
            Log::error('Google Drive get file info failed', ['file_id' => $fileId, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get file info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search files
     */
    public function searchFiles(Request $request): JsonResponse
    {
        try {
            if (!$this->googleDriveService->isAuthenticated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated with Google Drive'
                ], 401);
            }
            
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:1|max:255',
                'page_size' => 'nullable|integer|min:1|max:100'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $query = $request->get('query');
            $pageSize = $request->get('page_size', 10);
            
            $result = $this->googleDriveService->searchFiles($query, $pageSize);
            
            return response()->json($result);
            
        } catch (Exception $e) {
            Log::error('Google Drive search failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search files: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authentication status
     */
    public function getAuthStatus(): JsonResponse
    {
        try {
            $isAuthenticated = $this->googleDriveService->isAuthenticated();
            $authUrl = $isAuthenticated ? null : $this->googleDriveService->getAuthUrl();
            
            return response()->json([
                'success' => true,
                'authenticated' => $isAuthenticated,
                'auth_url' => $authUrl
            ]);
            
        } catch (Exception $e) {
            Log::error('Google Drive auth status check failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check authentication status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disconnect from Google Drive
     */
    public function disconnect(): JsonResponse
    {
        try {
            // Clear stored tokens
            \Illuminate\Support\Facades\Cache::forget('googledrive_access_token');
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully disconnected from Google Drive'
            ]);
            
        } catch (Exception $e) {
            Log::error('Google Drive disconnect failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to disconnect: ' . $e->getMessage()
            ], 500);
        }
    }
}