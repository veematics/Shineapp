@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="icon me-2">
                            <svg class="icon">
                                <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-apps-settings') }}"></use>
                            </svg>
                        </i>
                        Application Settings
                    </h4>
                    <small class="text-body-secondary">Configure your application settings</small>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.appsetting.update') }}" enctype="multipart/form-data" id="appSettingForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="active_tab" id="activeTabInput" value="globalsetting">
                        
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                             <li class="nav-item" role="presentation">
                                <button class="nav-link show active" id="global-setting-tab" data-coreui-toggle="tab" data-coreui-target="#globalsetting" type="button" role="tab" aria-controls="globalsetting" aria-selected="false">
                                    <i class="icon me-2">
                                        <svg class="icon">
                                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-settings') }}"></use>
                                        </svg>
                                    </i>
                                    Global Setting
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link " id="credentials-tab" data-coreui-toggle="tab" data-coreui-target="#credentials" type="button" role="tab" aria-controls="credentials" aria-selected="true">
                                    <i class="icon me-2">
                                        <svg class="icon">
                                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                                        </svg>
                                    </i>
                                    Application Credentials
                                </button>
                            </li>
                           
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="ai-integration-tab" data-coreui-toggle="tab" data-coreui-target="#ai-integration" type="button" role="tab" aria-controls="ai-integration" aria-selected="false">
                                    <i class="icon me-2">
                                        <svg class="icon">
                                            <use xlink:href="{{ asset('vendor/fontawesome/svgs/light/brain.svg') }}"></use>
                                        </svg>
                                    </i>
                                    AI Integration
                                </button>
                            </li>
                            
                           
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="settingsTabsContent">
                             <!-- Application Credentials Tab -->
                            <div class="tab-pane fade  show active" id="globalsetting" role="tabpanel" aria-labelledby="global-setting-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                         123123123123 123123 123 123 123 123 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Application Credentials Tab -->
                            <div class="tab-pane fade" id="credentials" role="tabpanel" aria-labelledby="credentials-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="appName" class="form-label">Application Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('appName') is-invalid @enderror" id="appName" name="appName" value="{{ old('appName', $appSetting->appName) }}" required>
                                            @error('appName')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="appHeadline" class="form-label">Application Headline</label>
                                            <input type="text" class="form-control @error('appHeadline') is-invalid @enderror" id="appHeadline" name="appHeadline" value="{{ old('appHeadline', $appSetting->appHeadline) }}">
                                            @error('appHeadline')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="appAIFallbackModel" class="form-label">Fallback Model</label>
                                            <input type="hidden" class="form-control" id="appAIFallbackModel" name="appAIFallbackModel" value="{{ old('appAIFallbackModel', is_array($appSetting->appAIFallbackModel) ? json_encode($appSetting->appAIFallbackModel) : $appSetting->appAIFallbackModel) }}">
                                            <div class="form-text">JSON format storing provider and model for fallback purposes</div>
                                            @error('appAIFallbackModel')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                            <label for="appLogoBig" class="form-label">Large Logo</label>
                            <input type="file" class="form-control @error('appLogoBig') is-invalid @enderror" id="appLogoBig" name="appLogoBig" accept="image/*">
                            <div class="form-text">Upload a large logo image (JPG, PNG, WebP, SVG)</div>
                            @if($appSetting->appLogoBig && $appSetting->appLogoBig !== '0')
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $appSetting->appLogoBig) }}" alt="Large Logo" class="img-thumbnail" style="max-width: 200px; max-height: 100px;">
                                    <small class="text-muted d-block">Current large logo</small>
                                </div>
                            @endif
                            @error('appLogoBig')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                            <label for="appLogoSmall" class="form-label">Small Logo</label>
                            <input type="file" class="form-control @error('appLogoSmall') is-invalid @enderror" id="appLogoSmall" name="appLogoSmall" accept="image/*">
                            <div class="form-text">Upload a small logo image (JPG, PNG, WebP, SVG)</div>
                            @if($appSetting->appLogoSmall && $appSetting->appLogoSmall !== '0')
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $appSetting->appLogoSmall) }}" alt="Small Logo" class="img-thumbnail" style="max-width: 100px; max-height: 50px;">
                                    <small class="text-muted d-block">Current small logo</small>
                                </div>
                            @endif
                            @error('appLogoSmall')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                            <label for="appLogoBigDark" class="form-label">Large Logo (Dark Theme)</label>
                            <input type="file" class="form-control @error('appLogoBigDark') is-invalid @enderror" id="appLogoBigDark" name="appLogoBigDark" accept="image/*">
                            <div class="form-text">Upload a large logo for dark theme (JPG, PNG, WebP, SVG)</div>
                            @if($appSetting->appLogoBigDark && $appSetting->appLogoBigDark !== '0')
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $appSetting->appLogoBigDark) }}" alt="Large Logo Dark" class="img-thumbnail" style="max-width: 200px; max-height: 100px;">
                                    <small class="text-muted d-block">Current large logo (dark theme)</small>
                                </div>
                            @endif
                            @error('appLogoBigDark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                            <label for="appLogoSmallDark" class="form-label">Small Logo (Dark Theme)</label>
                            <input type="file" class="form-control @error('appLogoSmallDark') is-invalid @enderror" id="appLogoSmallDark" name="appLogoSmallDark" accept="image/*">
                            <div class="form-text">Upload a small logo for dark theme (JPG, PNG, WebP, SVG)</div>
                            @if($appSetting->appLogoSmallDark && $appSetting->appLogoSmallDark !== '0')
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $appSetting->appLogoSmallDark) }}" alt="Small Logo Dark" class="img-thumbnail" style="max-width: 100px; max-height: 50px;">
                                    <small class="text-muted d-block">Current small logo (dark theme)</small>
                                </div>
                            @endif
                            @error('appLogoSmallDark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- AI Integration Tab -->
                            <div class="tab-pane fade" id="ai-integration" role="tabpanel" aria-labelledby="ai-integration-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="appopenaikey" class="form-label">OpenAI API Key</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('appopenaikey') is-invalid @enderror" id="appopenaikey" name="appopenaikey" value="{{ old('appopenaikey', $appSetting->appopenaikey !== '0' ? $appSetting->appopenaikey : '') }}">
                                                <button class="btn btn-outline-secondary" type="button" id="toggleOpenAI">
                                                    <i class="icon">
                                                        <svg class="icon">
                                                            <use xlink:href="{{ asset('vendor/coreui/icons/pro/cil-eye.svg') }}"></use>
                                                        </svg>
                                                    </i>
                                                </button>
                                            </div>
                                            <div class="form-text">Your OpenAI API key for AI services</div>
                                            @error('appopenaikey')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="appdeepseekkey" class="form-label">DeepSeek API Key</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('appdeepseekkey') is-invalid @enderror" id="appdeepseekkey" name="appdeepseekkey" value="{{ old('appdeepseekkey', $appSetting->appdeepseekkey !== '0' ? $appSetting->appdeepseekkey : '') }}">
                                                <button class="btn btn-outline-secondary" type="button" id="toggleDeepSeek">
                                                    <i class="icon">
                                                        <svg class="icon">
                                                            <use xlink:href="{{ asset('vendor/coreui/icons/pro/cil-eye.svg') }}"></use>
                                                        </svg>
                                                    </i>
                                                </button>
                                            </div>
                                            <div class="form-text">Your DeepSeek API key for AI services</div>
                                            @error('appdeepseekkey')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="appAITemperature" class="form-label">AI Temperature</label>
                                            <input type="number" class="form-control @error('appAITemperature') is-invalid @enderror" id="appAITemperature" name="appAITemperature" value="{{ old('appAITemperature', $appSetting->appAITemperature) }}" min="0" max="2" step="0.1">
                                            <div class="form-text">Controls randomness in AI responses (0.0 - 2.0)</div>
                                            @error('appAITemperature')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="appAiMaxToken" class="form-label">Max Tokens</label>
                                            <input type="number" class="form-control @error('appAiMaxToken') is-invalid @enderror" id="appAiMaxToken" name="appAiMaxToken" value="{{ old('appAiMaxToken', $appSetting->appAiMaxToken) }}" min="1" max="100000">
                                            <div class="form-text">Maximum number of tokens for AI responses</div>
                                            @error('appAiMaxToken')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="mt-4">Configure Available AI Model</h4>
                                        
                                        @if(empty($appSetting->getAIDefaultModels()))
                                            <div class="alert alert-info">
                                                <i class="icon me-2">
                                                    <svg class="icon">
                                                        <use xlink:href="{{ asset('vendor/coreui/icons/pro/cil-info.svg') }}"></use>
                                                    </svg>
                                                </i>
                                                No AI models configured. Please validate your API keys and fetch available models.
                                            </div>
                                            <button type="button" class="btn btn-primary" id="validateApiBtn">
                                                <i class="icon me-2">
                                                    <svg class="icon">
                                                        <use xlink:href="{{ asset('vendor/coreui/icons/pro/cil-cloud-download.svg') }}"></use>
                                                    </svg>
                                                </i>
                                                Validate API and Initiate Available Models
                                            </button>
                                        @else
                                            <div class="d-flex gap-2 mb-3">
                                                <button type="button" class="btn btn-primary" id="refreshModelsBtn">
                                                    <i class="icon me-2">
                                                        <svg class="icon">
                                                            <use xlink:href="{{ asset('vendor/coreui/icons/pro/cil-reload.svg') }}"></use>
                                                        </svg>
                                                    </i>
                                                    Refresh Models
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" id="clearModelsBtn">
                                                    <i class="icon me-2">
                                                        <svg class="icon">
                                                            <use xlink:href="{{ asset('vendor/coreui/icons/pro/cil-trash.svg') }}"></use>
                                                        </svg>
                                                    </i>
                                                    Clear Models
                                                </button>
                                            </div>
                                            
                                            <div id="currentModels">
                                                <h5>Current Selected Models:</h5>
                                                <div class="row">
                                                    @foreach($appSetting->getAIDefaultModels() as $provider => $models)
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h6 class="mb-0">{{ ucfirst($provider) }} Models</h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    @foreach($models as $model)
                                                                        <div class="form-check d-flex align-items-center justify-content-between">
                                                                            <div class="d-flex align-items-center">
                                                                                <input class="form-check-input" type="checkbox" name="appAIDefaultModel[{{ $provider }}][]" value="{{ $model }}" id="model_{{ $provider }}_{{ $loop->index }}" checked>
                                                                                <label class="form-check-label ms-2" for="model_{{ $provider }}_{{ $loop->index }}">
                                                                                    {{ $model }}
                                                                                </label>
                                                                            </div>
                                                                            <div class="d-flex align-items-center">
                                                                                <button type="button" class="btn btn-sm btn-outline-primary me-2 set-fallback-btn" 
                                                                                        data-provider="{{ $provider }}" 
                                                                                        data-model="{{ $model }}" 
                                                                                        title="Make Default Fallback">
                                                                                    <i class="icon">
                                                                                        <svg class="icon">
                                                                                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-star') }}"></use>
                                                                                        </svg>
                                                                                    </i>
                                                                                </button>
                                                                                <span class="fallback-indicator" style="display: none;">
                                                                                    <i class="icon text-warning">
                                                                                        <svg class="icon">
                                                                                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-star') }}"></use>
                                                                                        </svg>
                                                                                    </i>
                                                                                    <small class="text-muted ms-1">Default Fallback</small>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Loading indicator -->
                                        <div id="loadingIndicator" class="text-center mt-3" style="display: none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2">Fetching available models...</p>
                                        </div>
                                        
                                        <!-- Model selection area -->
                                        <div id="modelSelectionArea" style="display: none;">
                                            <h5 class="mt-4">Select Models to Use:</h5>
                                            <div class="row" id="modelsList">
                                                <!-- Models will be populated here via JavaScript -->
                                            </div>
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-success" id="saveSelectedModels">
                                                    <i class="icon me-2">
                                                        <svg class="icon">
                                                            <use xlink:href="{{ asset('vendor/coreui/icons/pro/cil-check.svg') }}"></use>
                                                        </svg>
                                                    </i>
                                                    Save Selected Models
                                                </button>
                                                <button type="button" class="btn btn-secondary" id="cancelModelSelection">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                           
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                <i class="icon me-2">
                                    <svg class="icon">
                                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-arrow-left') }}"></use>
                                    </svg>
                                </i>
                                Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="icon me-2">
                                    <svg class="icon">
                                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-save') }}"></use>
                                    </svg>
                                </i>
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab persistence functionality
    function updateUrlHash(tabId) {
        if (history.replaceState) {
            history.replaceState(null, null, '#' + tabId);
        } else {
            window.location.hash = '#' + tabId;
        }
    }
    
    function activateTabFromHash() {
        const hash = window.location.hash.substring(1); // Remove the # symbol
        if (hash) {
            // Map tab pane IDs to tab button IDs
            const tabMapping = {
                'globalsetting': 'global-setting-tab',
                'credentials': 'credentials-tab',
                'ai-integration': 'ai-integration-tab'
            };
            
            const tabButtonId = tabMapping[hash];
            const tabButton = document.getElementById(tabButtonId);
            const tabPane = document.getElementById(hash);
            
            if (tabButton && tabPane) {
                // Remove active classes from all tabs
                document.querySelectorAll('.nav-link').forEach(tab => {
                    tab.classList.remove('active', 'show');
                    tab.setAttribute('aria-selected', 'false');
                });
                
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('active', 'show');
                });
                
                // Activate the target tab
                tabButton.classList.add('active', 'show');
                tabButton.setAttribute('aria-selected', 'true');
                tabPane.classList.add('active', 'show');
            }
        }
    }
    
    // Listen for tab clicks to update URL hash
    document.querySelectorAll('[data-coreui-toggle="tab"]').forEach(tabButton => {
        tabButton.addEventListener('click', function() {
            const targetId = this.getAttribute('data-coreui-target').substring(1); // Remove the # symbol
            updateUrlHash(targetId);
        });
    });
    
    // Activate tab based on URL hash on page load
    activateTabFromHash();
    
    // Toggle password visibility for API keys
    const toggleOpenAI = document.getElementById('toggleOpenAI');
    const toggleDeepSeek = document.getElementById('toggleDeepSeek');
    const openAIInput = document.getElementById('appopenaikey');
    const deepSeekInput = document.getElementById('appdeepseekkey');
    
    if (toggleOpenAI && openAIInput) {
        toggleOpenAI.addEventListener('click', function() {
            const type = openAIInput.getAttribute('type') === 'password' ? 'text' : 'password';
            openAIInput.setAttribute('type', type);
            
            const icon = this.querySelector('use');
            const iconName = type === 'password' ? 'cil-eye' : 'cil-eye-slash';
            icon.setAttribute('xlink:href', `{{ asset('vendor/coreui/icons/pro/${iconName}.svg') }}`);
        });
    }
    
    if (toggleDeepSeek && deepSeekInput) {
        toggleDeepSeek.addEventListener('click', function() {
            const type = deepSeekInput.getAttribute('type') === 'password' ? 'text' : 'password';
            deepSeekInput.setAttribute('type', type);
            
            const icon = this.querySelector('use');
            const iconName = type === 'password' ? 'cil-eye' : 'cil-eye-slash';
            icon.setAttribute('xlink:href', `{{ asset('vendor/coreui/icons/pro/${iconName}.svg') }}`);
        });
    }
   
    // Auto-open tab with validation errors
    function openTabWithErrors() {
        // Fields in Application Credentials tab
        const credentialsFields = ['appName', 'appHeadline', 'appLogoBig', 'appLogoSmall', 'appLogoBigDark', 'appLogoSmallDark'];
        // Fields in AI Integration tab
        const aiFields = ['appopenaikey', 'appdeepseekkey', 'appAITemperature', 'appAiMaxToken'];
        
        let hasCredentialsError = false;
        let hasAiError = false;
        
        // Check for errors in credentials tab
        credentialsFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field && field.classList.contains('is-invalid')) {
                hasCredentialsError = true;
            }
        });
        
        // Check for errors in AI tab
        aiFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field && field.classList.contains('is-invalid')) {
                hasAiError = true;
            }
        });
        
        // Open the appropriate tab
        if (hasCredentialsError) {
            // Open Application Credentials tab
            const credentialsTabButton = document.querySelector('[data-coreui-target="#credentials"]');
            if (credentialsTabButton) {
                const tab = new coreui.Tab(credentialsTabButton);
                tab.show();
            }
        } else if (hasAiError) {
            // Open AI Integration tab
            const aiTabButton = document.querySelector('[data-coreui-target="#ai-integration"]');
            if (aiTabButton) {
                const tab = new coreui.Tab(aiTabButton);
                tab.show();
            }
        }
    }
    
    // Run the function on page load to handle server-side validation errors
    openTabWithErrors();
    
    // Form validation and active tab capture
    const form = document.getElementById('appSettingForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Capture the currently active tab from the settings tabs only
            const activeTab = document.querySelector('#settingsTabsContent .tab-pane.active');
            const activeTabInput = document.getElementById('activeTabInput');
            if (activeTab && activeTabInput) {
                activeTabInput.value = activeTab.id;
            }
            
            const appName = document.getElementById('appName');
            if (!appName.value.trim()) {
                e.preventDefault();
                appName.focus();
                alert('Application name is required.');
                return false;
            }
        });
    }
    
    // API Model Validation and Fetching
    const validateApiBtn = document.getElementById('validateApiBtn');
    const refreshModelsBtn = document.getElementById('refreshModelsBtn');
    const clearModelsBtn = document.getElementById('clearModelsBtn');
    const saveSelectedModelsBtn = document.getElementById('saveSelectedModels');
    const cancelModelSelectionBtn = document.getElementById('cancelModelSelection');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const modelSelectionArea = document.getElementById('modelSelectionArea');
    const modelsList = document.getElementById('modelsList');
    
    // Excluded model keywords
    const excludedKeywords = ['preview', 'dall', 'image', 'tts', 'transcribe', 'codex', 'audio', '2024', 'whisper'];
    
    function validateApiKeys() {
        const openAIKey = document.getElementById('appopenaikey').value.trim();
        const deepSeekKey = document.getElementById('appdeepseekkey').value.trim();
        
        if (!openAIKey && !deepSeekKey) {
            alert('Please set at least one API key (OpenAI or DeepSeek) before validating.');
            // Switch to AI Integration tab
            const aiTab = document.getElementById('ai-integration-tab');
            if (aiTab) aiTab.click();
            return false;
        }
        
        if (!openAIKey) {
            const confirmProceed = confirm('OpenAI API key is not set. Do you want to proceed with only DeepSeek models?');
            if (!confirmProceed) return false;
        }
        
        if (!deepSeekKey) {
            const confirmProceed = confirm('DeepSeek API key is not set. Do you want to proceed with only OpenAI models?');
            if (!confirmProceed) return false;
        }
        
        return true;
    }
    
    function shouldExcludeModel(modelId) {
        const modelLower = modelId.toLowerCase();
        return excludedKeywords.some(keyword => modelLower.includes(keyword));
    }
    
    function getCurrentlySelectedModels() {
        const currentlySelected = {};
        const currentModelCheckboxes = document.querySelectorAll('#currentModels input[type="checkbox"]:checked');
        
        currentModelCheckboxes.forEach(checkbox => {
            const match = checkbox.name.match(/appAIDefaultModel\[(.+?)\]\[\]/);
            if (match) {
                const provider = match[1];
                if (!currentlySelected[provider]) {
                    currentlySelected[provider] = [];
                }
                currentlySelected[provider].push(checkbox.value);
            }
        });
        
        return currentlySelected;
    }
    
    function fetchModels() {
        if (!validateApiKeys()) return;
        
        loadingIndicator.style.display = 'block';
        modelSelectionArea.style.display = 'none';
        
        const openAIKey = document.getElementById('appopenaikey').value.trim();
        const deepSeekKey = document.getElementById('appdeepseekkey').value.trim();
        
        fetch('{{ route("admin.appsetting.fetch-models") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                openai_key: openAIKey,
                deepseek_key: deepSeekKey
            })
        })
        .then(response => response.json())
        .then(data => {
            loadingIndicator.style.display = 'none';
            
            if (data.success) {
                displayModels(data.models);
            } else {
                alert('Error fetching models: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            loadingIndicator.style.display = 'none';
            console.error('Error:', error);
            alert('Error fetching models. Please check your API keys and try again.');
        });
    }
    
    function displayModels(models) {
        modelsList.innerHTML = '';
        
        // Get currently selected models from the form
        const currentlySelected = getCurrentlySelectedModels();
        
        Object.keys(models).forEach(provider => {
            const providerModels = models[provider].filter(model => !shouldExcludeModel(model.id));
            
            if (providerModels.length > 0) {
                const providerDiv = document.createElement('div');
                providerDiv.className = 'col-md-6';
                
                providerDiv.innerHTML = `
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">${provider.charAt(0).toUpperCase() + provider.slice(1)} Models</h6>
                            <small class="text-muted">${providerModels.length} models available</small>
                        </div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            ${providerModels.map(model => {
                                const isSelected = currentlySelected[provider] && currentlySelected[provider].includes(model.id);
                                return `
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="selected_models[${provider}][]" value="${model.id}" id="new_model_${provider}_${model.id.replace(/[^a-zA-Z0-9]/g, '_')}" ${isSelected ? 'checked' : ''}>
                                        <label class="form-check-label" for="new_model_${provider}_${model.id.replace(/[^a-zA-Z0-9]/g, '_')}">
                                            <strong>${model.id}</strong>
                                            ${model.created ? `<br><small class="text-muted">Created: ${new Date(model.created * 1000).toLocaleDateString()}</small>` : ''}
                                        </label>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
                
                modelsList.appendChild(providerDiv);
            }
        });
        
        modelSelectionArea.style.display = 'block';
    }
    
    function saveSelectedModels() {
        const selectedModels = {};
        const checkboxes = document.querySelectorAll('input[name^="selected_models"]:checked');
        
        checkboxes.forEach(checkbox => {
            const match = checkbox.name.match(/selected_models\[(.+?)\]\[\]/);
            if (match) {
                const provider = match[1];
                if (!selectedModels[provider]) {
                    selectedModels[provider] = [];
                }
                selectedModels[provider].push(checkbox.value);
            }
        });
        
        if (Object.keys(selectedModels).length === 0) {
            alert('Please select at least one model.');
            return;
        }
        
        // Add hidden inputs to the form
        const form = document.getElementById('appSettingForm');
        
        // Remove existing appAIDefaultModel inputs
        const existingInputs = form.querySelectorAll('input[name^="appAIDefaultModel"]');
        existingInputs.forEach(input => input.remove());
        
        // Add new inputs
        Object.keys(selectedModels).forEach(provider => {
            selectedModels[provider].forEach(model => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `appAIDefaultModel[${provider}][]`;
                input.value = model;
                form.appendChild(input);
            });
        });
        
        // Set active tab to ai-integration before submitting
        document.getElementById('activeTabInput').value = 'ai-integration';
        
        // Submit the form
        form.submit();
    }
    
    function clearModels() {
        if (confirm('Are you sure you want to clear all selected models?')) {
            const form = document.getElementById('appSettingForm');
            
            // Add hidden input to clear models
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'clear_models';
            input.value = '1';
            form.appendChild(input);
            
            form.submit();
        }
    }
    
    // Event listeners
    if (validateApiBtn) {
        validateApiBtn.addEventListener('click', fetchModels);
    }
    
    if (refreshModelsBtn) {
        refreshModelsBtn.addEventListener('click', fetchModels);
    }
    
    if (clearModelsBtn) {
        clearModelsBtn.addEventListener('click', clearModels);
    }
    
    if (saveSelectedModelsBtn) {
        saveSelectedModelsBtn.addEventListener('click', saveSelectedModels);
    }
    
    if (cancelModelSelectionBtn) {
        cancelModelSelectionBtn.addEventListener('click', function() {
            modelSelectionArea.style.display = 'none';
        });
    }
    
    // Fallback model functionality
    function setFallbackModel(provider, model) {
        const fallbackData = {
            provider: provider,
            model: model
        };
        
        // Update hidden input
        document.getElementById('appAIFallbackModel').value = JSON.stringify(fallbackData);
        
        // Update UI indicators
        updateFallbackIndicators(provider, model);
        
        // Submit the form to save the fallback model
        const form = document.getElementById('appSettingForm');
        
        // Set active tab to ai-integration before submitting
        document.getElementById('activeTabInput').value = 'ai-integration';
        
        // Submit the form
        form.submit();
    }
    
    function updateFallbackIndicators(selectedProvider, selectedModel) {
        // Hide all fallback indicators and show all set buttons
        document.querySelectorAll('.fallback-indicator').forEach(indicator => {
            indicator.style.display = 'none';
        });
        document.querySelectorAll('.set-fallback-btn').forEach(btn => {
            btn.style.display = 'inline-block';
        });
        
        // Show indicator for selected fallback model and hide its button
        document.querySelectorAll('.set-fallback-btn').forEach(btn => {
            if (btn.dataset.provider === selectedProvider && btn.dataset.model === selectedModel) {
                btn.style.display = 'none';
                btn.parentElement.querySelector('.fallback-indicator').style.display = 'inline-flex';
            }
        });
    }
    
    function initializeFallbackModel() {
        const fallbackInput = document.getElementById('appAIFallbackModel');
        if (fallbackInput && fallbackInput.value) {
            try {
                const fallbackData = JSON.parse(fallbackInput.value);
                if (fallbackData.provider && fallbackData.model) {
                    updateFallbackIndicators(fallbackData.provider, fallbackData.model);
                }
            } catch (e) {
                console.log('No valid fallback model data found');
            }
        }
    }
    
    // Add event listeners for fallback buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.set-fallback-btn')) {
            const btn = e.target.closest('.set-fallback-btn');
            const provider = btn.dataset.provider;
            const model = btn.dataset.model;
            
            if (confirm(`Set ${model} (${provider}) as the default fallback model?`)) {
                setFallbackModel(provider, model);
            }
        }
    });
    
    // Initialize fallback model display on page load
    initializeFallbackModel();
});
</script>
@endpush