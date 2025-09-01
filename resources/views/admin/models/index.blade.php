@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Model Management</h4>
                    <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#createModelPermissionModal">
                        <i class="cil-plus"></i> Create Model Permission
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Model Name</th>
                                    <th>Class</th>
                                    <th>Related Permissions</th>
                                    <th>Associated Roles</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($models as $model)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $model['display_name'] ?? $model['name'] }}</strong>
                                            @if(!empty($model['description']))
                                                <br><small class="text-muted">{{ $model['description'] }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $model['class'] }}</code>
                                    </td>
                                    <td>
                                        @if(isset($model['permissions']) && (is_countable($model['permissions']) ? count($model['permissions']) : $model['permissions']->count()) > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($model['permissions'] as $permission)
                                                    <span class="badge bg-info">{{ is_object($permission) ? $permission->name : $permission }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">No permissions found</span>
                                        @endif
                                        @if(!empty($model['suggested_permissions']))
                                            <br><small class="text-info">Suggested: 
                                            @foreach($model['suggested_permissions'] as $suggested)
                                                <span class="badge bg-light text-dark me-1">{{ $suggested }}</span>
                                            @endforeach
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($model['roles']) && (is_countable($model['roles']) ? count($model['roles']) : $model['roles']->count()) > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($model['roles'] as $role)
                                                    <span class="badge bg-success">{{ is_object($role) ? $role->name : $role }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">No roles assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewModelDetails('{{ $model['name'] }}')" 
                                                    data-coreui-toggle="modal" 
                                                    data-coreui-target="#modelDetailsModal">
                                                <i class="cil-info"></i> Details
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="manageModelPermissions('{{ $model['name'] }}')" 
                                                    data-coreui-toggle="modal" 
                                                    data-coreui-target="#managePermissionsModal">
                                                <i class="cil-settings"></i> Manage
                                            </button>
                                            @if(!empty($model['suggested_permissions']))
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    onclick="createSuggestedPermissions('{{ $model['name'] }}', {{ json_encode($model['suggested_permissions']) }})" 
                                                    title="Create Suggested Permissions">
                                                <i class="cil-plus"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="cil-info"></i> No models found in the application.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Model Permission Modal -->
<div class="modal fade" id="createModelPermissionModal" tabindex="-1" aria-labelledby="createModelPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModelPermissionModalLabel">Create Model Permission</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createModelPermissionForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modelSelect" class="form-label">Select Model</label>
                                <select class="form-select" id="modelSelect" name="model" required>
                                    <option value="">Choose a model...</option>
                                    @foreach($models as $model)
                                        <option value="{{ $model['name'] }}">{{ $model['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="permissionType" class="form-label">Permission Type</label>
                                <select class="form-select" id="permissionType" name="permission_type" required>
                                    <option value="">Choose permission type...</option>
                                    <option value="view">View</option>
                                    <option value="create">Create</option>
                                    <option value="edit">Edit</option>
                                    <option value="delete">Delete</option>
                                    <option value="manage">Manage</option>
                                    <option value="approve">Approve</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="permissionName" class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="permissionName" name="permission_name" 
                               placeholder="e.g., view users, create posts" required>
                        <div class="form-text">Permission name will be auto-generated based on model and type selection.</div>
                    </div>
                    <div class="mb-3">
                        <label for="permissionDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="permissionDescription" name="description" rows="3" 
                                  placeholder="Describe what this permission allows..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign to Roles (Optional)</label>
                        <div class="row">
                            @foreach($allRoles as $role)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $role->id }}" 
                                           id="role_{{ $role->id }}" name="roles[]">
                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createModelPermission()">Create Permission</button>
            </div>
        </div>
    </div>
</div>

<!-- Model Details Modal -->
<div class="modal fade" id="modelDetailsModal" tabindex="-1" aria-labelledby="modelDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelDetailsModalLabel">Model Details</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modelDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Manage Permissions Modal -->
<div class="modal fade" id="managePermissionsModal" tabindex="-1" aria-labelledby="managePermissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="managePermissionsModalLabel">Manage Model Permissions</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="managePermissionsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="savePermissionChanges()">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-generate permission name based on model and type selection
document.getElementById('modelSelect').addEventListener('change', updatePermissionName);
document.getElementById('permissionType').addEventListener('change', updatePermissionName);

function updatePermissionName() {
    const model = document.getElementById('modelSelect').value;
    const type = document.getElementById('permissionType').value;
    const permissionNameField = document.getElementById('permissionName');
    
    if (model && type) {
        const modelLower = model.toLowerCase();
        permissionNameField.value = `${type} ${modelLower}`;
    }
}

// View model details
function viewModelDetails(modelName) {
    const content = document.getElementById('modelDetailsContent');
    content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    
    // Find model data
    const models = @json($models);
    const model = models.find(m => m.name === modelName);
    
    if (model) {
        let html = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Model Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Name:</strong></td><td>${model.name}</td></tr>
                        <tr><td><strong>Class:</strong></td><td><code>${model.class}</code></td></tr>
                        <tr><td><strong>Permissions:</strong></td><td>${model.permissions.length}</td></tr>
                        <tr><td><strong>Roles:</strong></td><td>${model.roles.length}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Related Permissions</h6>
                    <div class="mb-3">`;
        
        if (model.permissions.length > 0) {
            model.permissions.forEach(permission => {
                html += `<span class="badge bg-info me-1 mb-1">${permission.name}</span>`;
            });
        } else {
            html += '<span class="text-muted">No permissions found</span>';
        }
        
        html += `</div>
                    <h6>Associated Roles</h6>
                    <div>`;
        
        if (model.roles.length > 0) {
            model.roles.forEach(role => {
                html += `<span class="badge bg-success me-1 mb-1">${role.name}</span>`;
            });
        } else {
            html += '<span class="text-muted">No roles assigned</span>';
        }
        
        html += `</div>
                </div>
            </div>`;
        
        content.innerHTML = html;
    }
}

// Manage model permissions
function manageModelPermissions(modelName) {
    const content = document.getElementById('managePermissionsContent');
    content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    
    // This would typically load via AJAX, but for now we'll show a placeholder
    setTimeout(() => {
        content.innerHTML = `
            <div class="alert alert-info">
                <h6>Manage Permissions for: ${modelName}</h6>
                <p>This feature allows you to:</p>
                <ul>
                    <li>Create new permissions for this model</li>
                    <li>Assign existing permissions to roles</li>
                    <li>Remove permissions from roles</li>
                    <li>Set up model-specific access controls</li>
                </ul>
                <p class="mb-0"><strong>Note:</strong> This functionality will be implemented in the next phase.</p>
            </div>
        `;
    }, 500);
}

// Create suggested permissions
function createSuggestedPermissions(modelName, suggestedPermissions) {
    if (!suggestedPermissions || suggestedPermissions.length === 0) {
        alert('No suggested permissions available for this model.');
        return;
    }
    
    if (confirm(`Create ${suggestedPermissions.length} suggested permissions for ${modelName}?\n\nPermissions: ${suggestedPermissions.join(', ')}`)) {
        // Create each suggested permission
        const promises = suggestedPermissions.map(permission => {
            const formData = new FormData();
            formData.append('name', permission);
            formData.append('model', modelName);
            
            return fetch('{{ route("admin.model-permissions.create") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        });
        
        Promise.all(promises)
            .then(responses => Promise.all(responses.map(r => r.json())))
            .then(results => {
                const successful = results.filter(r => r.success).length;
                const failed = results.length - successful;
                
                if (failed === 0) {
                    showMessage(`Successfully created ${successful} permissions!`, 'success');
                } else {
                    showMessage(`Created ${successful} permissions, ${failed} failed.`, 'warning');
                }
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while creating permissions.', 'error');
            });
    }
}

// Create model permission
function createModelPermission() {
    const form = document.getElementById('createModelPermissionForm');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.model-permissions.create") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Permission created successfully!', 'success');
            location.reload();
        } else {
            showMessage('Error creating permission: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while creating the permission.', 'error');
    });
}

// Save permission changes
function savePermissionChanges() {
    alert('Save permission changes functionality will be implemented in the next phase.');
}

// Show success/error messages
function showMessage(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endpush