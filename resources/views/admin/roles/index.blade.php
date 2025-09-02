@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Role Management</h4>
                    <button class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#createRoleModal">
                        <i class="cil-plus"></i> Create Role
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Role Name</th>
                                    <th>Permissions</th>
                                    <th>Users Count</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td>
                                        <strong>{{ $role->name }}</strong>
                                        @if($role->name === 'super-admin')
                                            <span class="badge bg-danger ms-2">Super Admin</span>
                                        @elseif($role->name === 'admin')
                                            <span class="badge bg-warning ms-2">Admin</span>
                                        @else
                                            <span class="badge bg-info ms-2">{{ ucfirst($role->name) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($role->permissions->count() > 0)
                                            @foreach($role->permissions->take(3) as $permission)
                                                <span class="badge bg-secondary me-1">{{ $permission->name }}</span>
                                            @endforeach
                                            @if($role->permissions->count() > 3)
                                                <span class="badge bg-light text-dark">+{{ $role->permissions->count() - 3 }} more</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No permissions</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $role->users->count() ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewRoleDetails('{{ $role->name }}')"
                                                data-coreui-toggle="modal" data-coreui-target="#roleDetailsModal">
                                            <i class="cil-info"></i> View
                                        </button>
                                        @if($role->name !== 'super-admin')
                                        <button class="btn btn-sm btn-outline-warning" onclick="editRole('{{ $role->name }}')"
                                                data-coreui-toggle="modal" data-coreui-target="#editRoleModal">
                                            <i class="cil-pencil"></i> Edit
                                        </button>
                                        @endif
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

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRoleModalLabel">Create New Role</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createRoleForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="roleName" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="roleName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="row">
                            @php
                                $allPermissions = \Spatie\Permission\Models\Permission::all();
                            @endphp
                            @foreach($allPermissions as $permission)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" 
                                           value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Role Details Modal -->
<div class="modal fade" id="roleDetailsModal" tabindex="-1" aria-labelledby="roleDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleDetailsModalLabel">Role Details</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="roleDetailsContent">
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
// Create role form submission
document.getElementById('createRoleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/api/roles/create', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Role created successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the role.');
    });
});

// View role details
function viewRoleDetails(roleName) {
    const role = @json($roles);
    const selectedRole = role.find(r => r.name === roleName);
    
    if (selectedRole) {
        let permissionsHtml = '';
        if (selectedRole.permissions && selectedRole.permissions.length > 0) {
            permissionsHtml = selectedRole.permissions.map(p => 
                `<span class="badge bg-secondary me-1 mb-1">${p.name}</span>`
            ).join('');
        } else {
            permissionsHtml = '<span class="text-muted">No permissions assigned</span>';
        }
        
        document.getElementById('roleDetailsContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Role Information</h6>
                    <p><strong>Name:</strong> ${selectedRole.name}</p>
                    <p><strong>Guard:</strong> ${selectedRole.guard_name || 'web'}</p>
                    <p><strong>Created:</strong> ${new Date(selectedRole.created_at).toLocaleDateString()}</p>
                </div>
                <div class="col-md-6">
                    <h6>Statistics</h6>
                    <p><strong>Users:</strong> ${selectedRole.users ? selectedRole.users.length : 0}</p>
                    <p><strong>Permissions:</strong> ${selectedRole.permissions ? selectedRole.permissions.length : 0}</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Permissions</h6>
                    <div>${permissionsHtml}</div>
                </div>
            </div>
        `;
    }
}

// Edit role function (placeholder)
function editRole(roleName) {
    alert('Edit role functionality would be implemented here for: ' + roleName);
}
</script>
@endpush