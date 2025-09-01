@extends('layouts.app')

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
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Permission Name</th>
                                    <th>Guard Name</th>
                                    <th>Roles Count</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $permission)
                                <tr>
                                    <td>
                                        <strong>{{ $permission->name }}</strong>
                                        @if(str_contains($permission->name, 'manage'))
                                            <span class="badge bg-warning ms-2">Management</span>
                                        @elseif(str_contains($permission->name, 'view'))
                                            <span class="badge bg-info ms-2">View</span>
                                        @elseif(str_contains($permission->name, 'create'))
                                            <span class="badge bg-success ms-2">Create</span>
                                        @elseif(str_contains($permission->name, 'edit'))
                                            <span class="badge bg-primary ms-2">Edit</span>
                                        @elseif(str_contains($permission->name, 'delete'))
                                            <span class="badge bg-danger ms-2">Delete</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $permission->guard_name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $permission->roles->count() ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $permission->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewPermissionDetails('{{ $permission->name }}')"
                                                data-coreui-toggle="modal" data-coreui-target="#permissionDetailsModal">
                                            <i class="cil-info"></i> View
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="editPermission('{{ $permission->name }}', {{ $permission->id }})"
                                                data-coreui-toggle="modal" data-coreui-target="#editPermissionModal">
                                            <i class="cil-pencil"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

<!-- Permission Details Modal -->
<div class="modal fade" id="permissionDetailsModal" tabindex="-1" aria-labelledby="permissionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionDetailsModalLabel">Permission Details</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="permissionDetailsContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Permission Modal -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPermissionModalLabel">Edit Permission</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPermissionForm">
                <div class="modal-body">
                    <input type="hidden" id="editPermissionId" name="id">
                    <div class="mb-3">
                        <label for="editPermissionName" class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="editPermissionName" name="name" required>
                        <div class="form-text">Use lowercase with spaces (e.g., "manage users", "view reports")</div>
                    </div>
                    <div class="mb-3">
                        <label for="editGuardName" class="form-label">Guard Name</label>
                        <select class="form-select" id="editGuardName" name="guard_name">
                            <option value="web">Web</option>
                            <option value="api">API</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign to Roles</label>
                        <div id="editPermissionRoles" class="border border-secondary-subtle rounded p-2 bg-body-tertiary" style="max-height: 200px; overflow-y: auto;">
                            <!-- Role checkboxes will be populated by JavaScript -->
                        </div>
                        <div class="form-text">Select roles that should have this permission</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

// View permission details
function viewPermissionDetails(permissionName) {
    const permissions = @json($permissions);
    const selectedPermission = permissions.find(p => p.name === permissionName);
    
    if (selectedPermission) {
        let rolesHtml = '';
        if (selectedPermission.roles && selectedPermission.roles.length > 0) {
            rolesHtml = selectedPermission.roles.map(r => 
                `<span class="badge bg-info me-1 mb-1">${r.name}</span>`
            ).join('');
        } else {
            rolesHtml = '<span class="text-muted">No roles assigned</span>';
        }
        
        document.getElementById('permissionDetailsContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Permission Information</h6>
                    <p><strong>Name:</strong> ${selectedPermission.name}</p>
                    <p><strong>Guard:</strong> ${selectedPermission.guard_name || 'web'}</p>
                    <p><strong>Created:</strong> ${new Date(selectedPermission.created_at).toLocaleDateString()}</p>
                </div>
                <div class="col-md-6">
                    <h6>Statistics</h6>
                    <p><strong>Roles:</strong> ${selectedPermission.roles ? selectedPermission.roles.length : 0}</p>
                    <p><strong>Updated:</strong> ${new Date(selectedPermission.updated_at).toLocaleDateString()}</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Assigned to Roles</h6>
                    <div>${rolesHtml}</div>
                </div>
            </div>
        `;
    }
}

// Edit permission function
function editPermission(permissionName, permissionId) {
    // Fetch detailed permission data from API
    fetch(`/api/permissions/${permissionId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
             if (data.success) {
                 const permission = data.permission;
                 const allRoles = data.allRoles;
                 
                 // Populate form fields
                 document.getElementById('editPermissionId').value = permission.id;
                 document.getElementById('editPermissionName').value = permission.name;
                 document.getElementById('editGuardName').value = permission.guard_name || 'web';
                 
                 // Display role checkboxes
                 let rolesHtml = '';
                 if (allRoles && allRoles.length > 0) {
                     rolesHtml = allRoles.map(role => {
                         const isAssigned = permission.roles && permission.roles.some(r => r.id === role.id);
                         return `
                             <div class="form-check mb-2">
                                 <input class="form-check-input" type="checkbox" 
                                        id="role_${role.id}" 
                                        value="${role.id}" 
                                        ${isAssigned ? 'checked' : ''}
                                        onchange="togglePermissionRole(${permission.id}, ${role.id}, this.checked)">
                                 <label class="form-check-label" for="role_${role.id}">
                                     ${role.name}
                                 </label>
                             </div>
                         `;
                     }).join('');
                 } else {
                     rolesHtml = '<span class="text-body-secondary">No roles available</span>';
                 }
                 document.getElementById('editPermissionRoles').innerHTML = rolesHtml;
             } else {
                 alert('Error loading permission details: ' + (data.error || 'Unknown error'));
             }
         })
         .catch(error => {
             console.error('Error:', error);
             alert('An error occurred while loading permission details.');
         });
 }

// Edit permission form submission
document.getElementById('editPermissionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const permissionId = formData.get('id');
    const data = {
        name: formData.get('name'),
        guard_name: formData.get('guard_name')
    };
    
    fetch(`/api/permissions/${permissionId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Permission updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the permission.');
    });
 });
 
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
         } else {
             // Revert checkbox state on error
             const checkbox = document.getElementById(`role_${roleId}`);
             if (checkbox) {
                 checkbox.checked = !isChecked;
             }
             alert('Error: ' + (data.error || 'Unknown error'));
         }
     })
     .catch(error => {
         // Revert checkbox state on error
         const checkbox = document.getElementById(`role_${roleId}`);
         if (checkbox) {
             checkbox.checked = !isChecked;
         }
         console.error('Error:', error);
         alert('An error occurred while updating role assignment.');
     });
 }
 </script>
 @endpush