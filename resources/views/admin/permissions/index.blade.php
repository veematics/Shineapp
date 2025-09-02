@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Permission Management</h4>
                    <button class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#createPermissionModal">
                        <i class="cil-plus"></i> Create Permission
                    </button>
                </div>
                <div class="card-body">
                    <div class="row" id="permissionGroups">
                        <!-- Permission groups will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Permission Modal -->
<div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPermissionModalLabel">Create New Permission</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createPermissionForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="permissionName" class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="permissionName" name="name" required>
                        <div class="form-text">Use lowercase with spaces (e.g., "manage users", "view reports")</div>
                    </div>
                    <div class="mb-3">
                        <label for="guardName" class="form-label">Guard Name</label>
                        <select class="form-select" id="guardName" name="guard_name">
                            <option value="web" selected>Web</option>
                            <option value="api">API</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Permission Group Details Modal -->
<div class="modal fade" id="permissionGroupModal" tabindex="-1" aria-labelledby="permissionGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionGroupModalLabel">Permission Group Details</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Stats Section -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Statistics</h6>
                            </div>
                            <div class="card-body" id="groupStats">
                                <!-- Stats will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    <!-- Permissions and Roles Section -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Permissions & Role Assignments</h6>
                            </div>
                            <div class="card-body" id="groupPermissions">
                                <!-- Permissions will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Group permissions by last word
function groupPermissions() {
    const permissions = @json($permissions);
    const groups = {};
    
    permissions.forEach(permission => {
        const words = permission.name.split(' ');
        const lastWord = words[words.length - 1];
        const action = words.slice(0, -1).join(' ');
        
        if (!groups[lastWord]) {
            groups[lastWord] = {
                name: lastWord,
                displayName: lastWord.charAt(0).toUpperCase() + lastWord.slice(1),
                permissions: [],
                actions: new Set(),
                totalRoles: 0
            };
        }
        
        groups[lastWord].permissions.push({
            ...permission,
            action: action || 'manage'
        });
        groups[lastWord].actions.add(action || 'manage');
        groups[lastWord].totalRoles += permission.roles ? permission.roles.length : 0;
    });
    
    return groups;
}

// Render permission groups
function renderPermissionGroups() {
    const groups = groupPermissions();
    const container = document.getElementById('permissionGroups');
    
    let html = '';
    Object.values(groups).forEach(group => {
        const actionsArray = Array.from(group.actions);
        const actionButtons = actionsArray.map(action => {
            const actionClass = getActionClass(action);
            return `<button class="btn btn-sm ${actionClass} me-1 mb-1" onclick="showGroupDetails('${group.name}', '${action}')">${action}</button>`;
        }).join('');
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 permission-group-card" onclick="showGroupDetails('${group.name}')" style="cursor: pointer;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">${group.displayName}</h5>
                            <span class="badge bg-primary">${group.permissions.length}</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Total Roles: ${group.totalRoles}</small>
                        </div>
                        <div class="action-buttons" onclick="event.stopPropagation();">
                            ${actionButtons}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Get action button class based on action type
function getActionClass(action) {
    const actionClasses = {
        'view': 'btn-outline-info',
        'create': 'btn-outline-success',
        'edit': 'btn-outline-warning',
        'delete': 'btn-outline-danger',
        'manage': 'btn-outline-primary'
    };
    return actionClasses[action] || 'btn-outline-secondary';
}

// Show group details in modal
function showGroupDetails(groupName, specificAction = null) {
    const groups = groupPermissions();
    const group = groups[groupName];
    
    if (!group) return;
    
    // Update modal title
    document.getElementById('permissionGroupModalLabel').textContent = 
        `${group.displayName} Permissions${specificAction ? ` - ${specificAction}` : ''}`;
    
    // Filter permissions if specific action is requested
    const filteredPermissions = specificAction 
        ? group.permissions.filter(p => p.action === specificAction)
        : group.permissions;
    
    // Render stats
    renderGroupStats(group, filteredPermissions);
    
    // Render permissions
    renderGroupPermissions(filteredPermissions);
    
    // Show modal
    const modal = new coreui.Modal(document.getElementById('permissionGroupModal'));
    modal.show();
}

// Render group statistics
function renderGroupStats(group, permissions) {
    const allRoles = @json($permissions->pluck('roles')->flatten()->unique('id')->values());
    const uniqueRoles = new Set();
    
    permissions.forEach(permission => {
        if (permission.roles) {
            permission.roles.forEach(role => uniqueRoles.add(role.name));
        }
    });
    
    const statsHtml = `
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <span>Total Permissions:</span>
                <strong>${permissions.length}</strong>
            </div>
        </div>
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <span>Unique Roles:</span>
                <strong>${uniqueRoles.size}</strong>
            </div>
        </div>
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <span>Total Assignments:</span>
                <strong>${permissions.reduce((sum, p) => sum + (p.roles ? p.roles.length : 0), 0)}</strong>
            </div>
        </div>
        <div class="mb-3">
            <h6>Actions Available:</h6>
            ${Array.from(group.actions).map(action => 
                `<span class="badge ${getActionClass(action).replace('btn-outline-', 'bg-')} me-1">${action}</span>`
            ).join('')}
        </div>
    `;
    
    document.getElementById('groupStats').innerHTML = statsHtml;
}

// Render group permissions with role editing
function renderGroupPermissions(permissions) {
    const allRoles = @json(\Spatie\Permission\Models\Role::all());
    
    let html = '';
    permissions.forEach(permission => {
        const assignedRoleIds = permission.roles ? permission.roles.map(r => r.id) : [];
        
        html += `
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">${permission.name}</h6>
                        <div>
                            <span class="badge bg-secondary me-2">${permission.guard_name}</span>
                            <span class="badge bg-info me-2">${permission.roles ? permission.roles.length : 0} roles</span>
                            <button class="btn btn-outline-danger btn-sm" 
                                    onclick="deletePermission(${permission.id}, '${permission.name}')"
                                    title="Delete Permission">
                                <svg class="icon">
                                    <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-trash"></use>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Assigned Roles:</h6>
                            <div class="assigned-roles mb-3">
                                ${permission.roles && permission.roles.length > 0 
                                    ? permission.roles.map(role => 
                                        `<span class="badge bg-success me-1 mb-1">${role.name}</span>`
                                    ).join('')
                                    : '<span class="text-muted">No roles assigned</span>'
                                }
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Available Roles:</h6>
                            <div class="role-checkboxes" style="max-height: 150px; overflow-y: auto;">
                                ${allRoles.map(role => {
                                    const isAssigned = assignedRoleIds.includes(role.id);
                                    return `
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="perm_${permission.id}_role_${role.id}" 
                                                   value="${role.id}" 
                                                   ${isAssigned ? 'checked' : ''}
                                                   onchange="togglePermissionRole(${permission.id}, ${role.id}, this.checked)">
                                            <label class="form-check-label" for="perm_${permission.id}_role_${role.id}">
                                                ${role.name}
                                            </label>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    document.getElementById('groupPermissions').innerHTML = html;
}

// Toggle permission role assignment
function togglePermissionRole(permissionId, roleId, isChecked) {
    const url = isChecked 
        ? `/api/permissions/${permissionId}/assign-role`
        : `/api/permissions/${permissionId}/revoke-role`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            role_id: roleId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message briefly
            const action = isChecked ? 'assigned to' : 'revoked from';
            console.log(`Permission ${action} role successfully`);
            
            // Refresh the current view to show updated assignments
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            // Revert checkbox state on error
            const checkbox = document.getElementById(`perm_${permissionId}_role_${roleId}`);
            if (checkbox) {
                checkbox.checked = !isChecked;
            }
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        // Revert checkbox state on error
        const checkbox = document.getElementById(`perm_${permissionId}_role_${roleId}`);
        if (checkbox) {
            checkbox.checked = !isChecked;
        }
        console.error('Error:', error);
        alert('An error occurred while updating role assignment.');
    });
}

// Delete permission function
function deletePermission(permissionId, permissionName) {
    if (!confirm(`Are you sure you want to delete the permission "${permissionName}"?\n\nThis action cannot be undone and will remove the permission from all roles.`)) {
        return;
    }
    
    fetch(`/api/permissions/${permissionId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Permission deleted successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the permission.');
    });
}

// Create permission form submission
document.getElementById('createPermissionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        name: formData.get('name'),
        guard_name: formData.get('guard_name')
    };
    
    fetch('/api/permissions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Permission created successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the permission.');
    });
});

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    renderPermissionGroups();
});

// Add hover effects for cards
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .permission-group-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .permission-group-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .action-buttons .btn {
            transition: all 0.2s ease-in-out;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endpush