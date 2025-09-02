@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">User Management</h4>
                    <div class="card-header-actions">
                        <button class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#assignRoleModal">
                            <i class="cil-user-plus"></i> Assign Role
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Permissions</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <div class="avatar-initial bg-primary rounded-circle">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                <br>
                                                <small class="text-muted">ID: {{ $user->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                @if($role->name === 'super-admin')
                                                    <span class="badge bg-danger me-1">{{ $role->name }}</span>
                                                @elseif($role->name === 'admin')
                                                    <span class="badge bg-warning me-1">{{ $role->name }}</span>
                                                @else
                                                    <span class="badge bg-info me-1">{{ $role->name }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-muted">No roles</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $allPermissions = $user->getAllPermissions();
                                        @endphp
                                        @if($allPermissions->count() > 0)
                                            @foreach($allPermissions->take(2) as $permission)
                                                <span class="badge bg-secondary me-1">{{ $permission->name }}</span>
                                            @endforeach
                                            @if($allPermissions->count() > 2)
                                                <span class="badge bg-light text-dark">+{{ $allPermissions->count() - 2 }} more</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No permissions</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Unverified</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewUserDetails({{ $user->id }})"
                                                    data-coreui-toggle="modal" data-coreui-target="#userDetailsModal">
                                                <i class="cil-info"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success" 
                                                    onclick="manageUserRoles({{ $user->id }}, '{{ $user->name }}')"
                                                    data-coreui-toggle="modal" data-coreui-target="#manageRolesModal">
                                                <i class="cil-settings"></i>
                                            </button>
                                        </div>
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

<!-- Assign Role Modal -->
<div class="modal fade" id="assignRoleModal" tabindex="-1" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignRoleModalLabel">Assign Role to User</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignRoleForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="userId" class="form-label">Select User</label>
                        <select class="form-select" id="userId" name="user_id" required>
                            <option value="">Choose a user...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="roleSelect" class="form-label">Select Role</label>
                        <select class="form-select" id="roleSelect" name="role" required>
                            <option value="">Choose a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Manage User Roles Modal -->
<div class="modal fade" id="manageRolesModal" tabindex="-1" aria-labelledby="manageRolesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageRolesModalLabel">Manage User Roles</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="manageRolesContent">
                <!-- Content will be loaded dynamically -->
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
const users = @json($users);
const roles = @json($roles);

// Assign role form submission
document.getElementById('assignRoleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/api/assign-role', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Role assigned successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning the role.');
    });
});

// View user details
function viewUserDetails(userId) {
    const user = users.find(u => u.id === userId);
    
    if (user) {
        let rolesHtml = '';
        if (user.roles && user.roles.length > 0) {
            rolesHtml = user.roles.map(r => 
                `<span class="badge bg-info me-1 mb-1">${r.name}</span>`
            ).join('');
        } else {
            rolesHtml = '<span class="text-muted">No roles assigned</span>';
        }
        
        let permissionsHtml = '';
        // Note: In a real implementation, you'd fetch all permissions via API
        permissionsHtml = '<span class="text-muted">Permissions loaded via roles</span>';
        
        document.getElementById('userDetailsContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>User Information</h6>
                    <p><strong>Name:</strong> ${user.name}</p>
                    <p><strong>Email:</strong> ${user.email}</p>
                    <p><strong>Verified:</strong> ${user.email_verified_at ? 'Yes' : 'No'}</p>
                    <p><strong>Created:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
                </div>
                <div class="col-md-6">
                    <h6>Access Information</h6>
                    <p><strong>Roles:</strong> ${user.roles ? user.roles.length : 0}</p>
                    <p><strong>Last Login:</strong> N/A</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Assigned Roles</h6>
                    <div>${rolesHtml}</div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Effective Permissions</h6>
                    <div>${permissionsHtml}</div>
                </div>
            </div>
        `;
    }
}

// Manage user roles
function manageUserRoles(userId, userName) {
    const user = users.find(u => u.id === userId);
    
    if (user) {
        let currentRolesHtml = '';
        if (user.roles && user.roles.length > 0) {
            currentRolesHtml = user.roles.map(role => `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-info">${role.name}</span>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeRole(${userId}, '${role.name}')">
                        <i class="cil-x"></i>
                    </button>
                </div>
            `).join('');
        } else {
            currentRolesHtml = '<p class="text-muted">No roles assigned</p>';
        }
        
        let availableRolesHtml = roles.map(role => {
            const hasRole = user.roles && user.roles.some(r => r.name === role.name);
            return `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" ${hasRole ? 'checked disabled' : ''} 
                           id="role_${role.id}" onchange="toggleRole(${userId}, '${role.name}', this.checked)">
                    <label class="form-check-label" for="role_${role.id}">
                        ${role.name}
                    </label>
                </div>
            `;
        }).join('');
        
        document.getElementById('manageRolesContent').innerHTML = `
            <h6>Managing roles for: <strong>${userName}</strong></h6>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6>Current Roles</h6>
                    ${currentRolesHtml}
                </div>
                <div class="col-md-6">
                    <h6>Available Roles</h6>
                    ${availableRolesHtml}
                </div>
            </div>
        `;
    }
}

// Toggle role assignment
function toggleRole(userId, roleName, assign) {
    const action = assign ? 'assign-role' : 'remove-role';
    
    fetch(`/api/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            user_id: userId,
            role: roleName
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the page to show updated roles
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred.');
    });
}

// Remove role
function removeRole(userId, roleName) {
    if (confirm(`Are you sure you want to remove the role "${roleName}"?`)) {
        toggleRole(userId, roleName, false);
    }
}
</script>
@endpush