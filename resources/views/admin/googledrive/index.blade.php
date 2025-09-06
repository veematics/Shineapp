@extends('layouts.admin')

@section('title', 'Google Drive Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="cil-cloud-upload me-2"></i>
                        Google Drive Management
                    </h4>
                </div>
                <div class="card-body">
                    @if(!$isAuthenticated)
                        <!-- Authentication Required -->
                        <div class="alert alert-warning" role="alert">
                            <h5 class="alert-heading">
                                <i class="cil-warning me-2"></i>
                                Authentication Required
                            </h5>
                            <p class="mb-3">You need to authenticate with Google Drive to access file management features.</p>
                            <a href="{{ $authUrl }}" class="btn btn-primary">
                                <i class="cil-account-logout me-2"></i>
                                Connect to Google Drive
                            </a>
                        </div>
                    @else
                        <!-- File Management Interface -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success" data-coreui-toggle="modal" data-coreui-target="#uploadModal">
                                        <i class="cil-cloud-upload me-2"></i>
                                        Upload File
                                    </button>
                                    <button type="button" class="btn btn-info" data-coreui-toggle="modal" data-coreui-target="#createFolderModal">
                                        <i class="cil-folder me-2"></i>
                                        Create Folder
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="refreshFiles()">
                                        <i class="cil-reload me-2"></i>
                                        Refresh
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-2 justify-content-end">
                                    <div class="input-group" style="max-width: 300px;">
                                        <input type="text" class="form-control" id="searchInput" placeholder="Search files...">
                                        <button class="btn btn-outline-secondary" type="button" onclick="searchFiles()">
                                            <i class="cil-search"></i>
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-danger" onclick="disconnectGoogleDrive()">
                                        <i class="cil-account-logout me-2"></i>
                                        Disconnect
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Breadcrumb Navigation -->
                        <nav aria-label="breadcrumb" class="mb-3">
                            <ol class="breadcrumb" id="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#" onclick="navigateToFolder(null)">Root</a>
                                </li>
                            </ol>
                        </nav>

                        <!-- Files Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="40"><i class="cil-description"></i></th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Modified</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="filesTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 mb-0">Loading files...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Files pagination" class="mt-3">
                            <ul class="pagination justify-content-center" id="pagination">
                                <!-- Pagination will be populated by JavaScript -->
                            </ul>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($isAuthenticated)
<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">
                    <i class="cil-cloud-upload me-2"></i>
                    Upload File
                </h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Select File</label>
                        <input type="file" class="form-control" id="fileInput" name="file" required>
                        <div class="form-text">Maximum file size: {{ number_format(config('googledrive.max_file_size', 104857600) / 1024 / 1024, 0) }}MB</div>
                    </div>
                    <div class="mb-3">
                        <label for="fileName" class="form-label">File Name (Optional)</label>
                        <input type="text" class="form-control" id="fileName" name="name" placeholder="Leave empty to use original name">
                    </div>
                    <div class="mb-3">
                        <label for="fileDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="fileDescription" name="description" rows="3" placeholder="File description"></textarea>
                    </div>
                    <input type="hidden" id="currentFolderId" name="folder_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="cil-cloud-upload me-2"></i>
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFolderModalLabel">
                    <i class="cil-folder me-2"></i>
                    Create Folder
                </h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createFolderForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="folderName" class="form-label">Folder Name</label>
                        <input type="text" class="form-control" id="folderName" name="name" required placeholder="Enter folder name">
                    </div>
                    <input type="hidden" id="parentFolderId" name="parent_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="cil-folder me-2"></i>
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- File Info Modal -->
<div class="modal fade" id="fileInfoModal" tabindex="-1" aria-labelledby="fileInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileInfoModalLabel">
                    <i class="cil-info me-2"></i>
                    File Information
                </h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="fileInfoContent">
                <!-- File info will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@if($isAuthenticated)
@push('scripts')
<script>
let currentFolderId = null;
let currentPageToken = null;
let isLoading = false;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadFiles();
    
    // Setup form handlers
    setupUploadForm();
    setupCreateFolderForm();
});

// Load files from Google Drive
function loadFiles(folderId = null, pageToken = null) {
    if (isLoading) return;
    
    isLoading = true;
    currentFolderId = folderId;
    
    const params = new URLSearchParams();
    if (folderId) params.append('folder_id', folderId);
    if (pageToken) params.append('page_token', pageToken);
    
    fetch(`{{ route('admin.googledrive.files.list') }}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayFiles(data.files);
                updatePagination(data.nextPageToken);
                updateBreadcrumb(folderId);
            } else {
                showAlert('error', 'Failed to load files: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error loading files:', error);
            showAlert('error', 'Failed to load files. Please try again.');
        })
        .finally(() => {
            isLoading = false;
        });
}

// Display files in table
function displayFiles(files) {
    const tbody = document.getElementById('filesTableBody');
    
    if (files.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <i class="cil-folder-open text-muted" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0 text-muted">No files found</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = files.map(file => {
        const icon = file.isFolder ? 'cil-folder' : getFileIcon(file.mimeType);
        const size = file.isFolder ? '-' : formatFileSize(file.size);
        const modifiedDate = new Date(file.modifiedTime).toLocaleDateString();
        
        return `
            <tr>
                <td><i class="${icon} text-primary"></i></td>
                <td>
                    ${file.isFolder ? 
                        `<a href="#" onclick="navigateToFolder('${file.id}')" class="text-decoration-none">${file.name}</a>` : 
                        file.name
                    }
                </td>
                <td><span class="badge bg-secondary">${file.isFolder ? 'Folder' : getMimeTypeLabel(file.mimeType)}</span></td>
                <td>${size}</td>
                <td>${modifiedDate}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-info" onclick="showFileInfo('${file.id}')" title="Info">
                            <i class="cil-info"></i>
                        </button>
                        ${!file.isFolder ? `
                            <button type="button" class="btn btn-outline-success" onclick="downloadFile('${file.id}')" title="Download">
                                <i class="cil-cloud-download"></i>
                            </button>
                        ` : ''}
                        <button type="button" class="btn btn-outline-danger" onclick="deleteFile('${file.id}', '${file.name}')" title="Delete">
                            <i class="cil-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Navigate to folder
function navigateToFolder(folderId) {
    loadFiles(folderId);
}

// Refresh files
function refreshFiles() {
    loadFiles(currentFolderId);
}

// Search files
function searchFiles() {
    const query = document.getElementById('searchInput').value.trim();
    if (!query) {
        refreshFiles();
        return;
    }
    
    fetch(`{{ route('admin.googledrive.search') }}?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayFiles(data.files);
                document.getElementById('pagination').innerHTML = '';
            } else {
                showAlert('error', 'Search failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            showAlert('error', 'Search failed. Please try again.');
        });
}

// Setup upload form
function setupUploadForm() {
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.set('folder_id', currentFolderId || '');
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Uploading...';
        submitBtn.disabled = true;
        
        fetch('{{ route("admin.googledrive.files.upload") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'File uploaded successfully!');
                bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
                this.reset();
                refreshFiles();
            } else {
                showAlert('error', 'Upload failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            showAlert('error', 'Upload failed. Please try again.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
}

// Setup create folder form
function setupCreateFolderForm() {
    document.getElementById('createFolderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.set('parent_id', currentFolderId || '');
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Creating...';
        submitBtn.disabled = true;
        
        fetch('{{ route("admin.googledrive.folders.create") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Folder created successfully!');
                bootstrap.Modal.getInstance(document.getElementById('createFolderModal')).hide();
                this.reset();
                refreshFiles();
            } else {
                showAlert('error', 'Failed to create folder: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Create folder error:', error);
            showAlert('error', 'Failed to create folder. Please try again.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
}

// Download file
function downloadFile(fileId) {
    window.open(`{{ route('admin.googledrive.files.download', '') }}/${fileId}`, '_blank');
}

// Delete file
function deleteFile(fileId, fileName) {
    if (!confirm(`Are you sure you want to delete "${fileName}"?`)) {
        return;
    }
    
    fetch(`{{ route('admin.googledrive.files.delete', '') }}/${fileId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'File deleted successfully!');
            refreshFiles();
        } else {
            showAlert('error', 'Failed to delete file: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showAlert('error', 'Failed to delete file. Please try again.');
    });
}

// Show file info
function showFileInfo(fileId) {
    fetch(`{{ route('admin.googledrive.files.info', '') }}/${fileId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const file = data.file;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Basic Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Name:</strong></td><td>${file.name}</td></tr>
                                <tr><td><strong>Type:</strong></td><td>${file.mimeType}</td></tr>
                                <tr><td><strong>Size:</strong></td><td>${formatFileSize(file.size)}</td></tr>
                                <tr><td><strong>Created:</strong></td><td>${new Date(file.createdTime).toLocaleString()}</td></tr>
                                <tr><td><strong>Modified:</strong></td><td>${new Date(file.modifiedTime).toLocaleString()}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Links</h6>
                            <div class="d-grid gap-2">
                                ${file.webViewLink ? `<a href="${file.webViewLink}" target="_blank" class="btn btn-outline-primary btn-sm">View in Google Drive</a>` : ''}
                                ${file.webContentLink ? `<a href="${file.webContentLink}" target="_blank" class="btn btn-outline-success btn-sm">Direct Download</a>` : ''}
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('fileInfoContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('fileInfoModal')).show();
            } else {
                showAlert('error', 'Failed to get file info: ' + data.message);
            }
        })
        .catch(error => {
            console.error('File info error:', error);
            showAlert('error', 'Failed to get file info. Please try again.');
        });
}

// Disconnect from Google Drive
function disconnectGoogleDrive() {
    if (!confirm('Are you sure you want to disconnect from Google Drive?')) {
        return;
    }
    
    fetch('{{ route("admin.googledrive.disconnect") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Disconnected from Google Drive successfully!');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('error', 'Failed to disconnect: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Disconnect error:', error);
        showAlert('error', 'Failed to disconnect. Please try again.');
    });
}

// Utility functions
function formatFileSize(bytes) {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getFileIcon(mimeType) {
    if (mimeType.startsWith('image/')) return 'cil-image';
    if (mimeType.startsWith('video/')) return 'cil-video';
    if (mimeType.startsWith('audio/')) return 'cil-audio-spectrum';
    if (mimeType.includes('pdf')) return 'cil-description';
    if (mimeType.includes('document') || mimeType.includes('word')) return 'cil-description';
    if (mimeType.includes('spreadsheet') || mimeType.includes('excel')) return 'cil-spreadsheet';
    if (mimeType.includes('presentation') || mimeType.includes('powerpoint')) return 'cil-presentation';
    return 'cil-file';
}

function getMimeTypeLabel(mimeType) {
    if (mimeType.startsWith('image/')) return 'Image';
    if (mimeType.startsWith('video/')) return 'Video';
    if (mimeType.startsWith('audio/')) return 'Audio';
    if (mimeType.includes('pdf')) return 'PDF';
    if (mimeType.includes('document') || mimeType.includes('word')) return 'Document';
    if (mimeType.includes('spreadsheet') || mimeType.includes('excel')) return 'Spreadsheet';
    if (mimeType.includes('presentation') || mimeType.includes('powerpoint')) return 'Presentation';
    return 'File';
}

function updateBreadcrumb(folderId) {
    // Simple breadcrumb - in a real app, you'd track the folder hierarchy
    const breadcrumb = document.getElementById('breadcrumb');
    if (folderId) {
        breadcrumb.innerHTML = `
            <li class="breadcrumb-item">
                <a href="#" onclick="navigateToFolder(null)">Root</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Folder</li>
        `;
    } else {
        breadcrumb.innerHTML = `
            <li class="breadcrumb-item active" aria-current="page">Root</li>
        `;
    }
}

function updatePagination(nextPageToken) {
    const pagination = document.getElementById('pagination');
    if (nextPageToken) {
        pagination.innerHTML = `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadFiles(currentFolderId, '${nextPageToken}')">Next</a>
            </li>
        `;
    } else {
        pagination.innerHTML = '';
    }
}

function showAlert(type, message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Insert at top of card body
    const cardBody = document.querySelector('.card-body');
    cardBody.insertBefore(alertDiv, cardBody.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Handle search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchFiles();
    }
});
</script>
@endpush
@endif