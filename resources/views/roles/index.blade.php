@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark m-0">Roles & Module Permissions</h4>
        <span class="text-muted small">Configure role hierarchies, module access control, and user assignments</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-mg-gold btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Add System User
        </button>
    </div>
</div>

<!-- ROLES SUMMARY CARDS -->
<div class="row g-3 mb-4">
    @foreach($roles as $role)
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card-custom h-100 p-3 shadow-sm border">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">{{ $role->users->count() }} Users</span>
                    @if($role->slug !== 'super-admin')
                        <a href="{{ route('roles.editPermissions', $role->id) }}" class="btn btn-sm btn-light border py-0 px-2 text-primary" title="Edit Permissions">
                            <i class="bi bi-shield-check"></i> Edit
                        </a>
                    @else
                        <span class="badge bg-success bg-opacity-10 text-success text-xs">All Access</span>
                    @endif
                </div>
                <h6 class="fw-bold text-dark mb-1">{{ $role->name }}</h6>
                <p class="text-xs text-muted mb-3" style="min-height: 32px;">{{ $role->description }}</p>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top text-xs text-secondary">
                    <span>Permissions:</span>
                    <strong class="text-dark">{{ $role->slug === 'super-admin' ? 'Unlimited' : $role->permissions->count() . ' modules' }}</strong>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- SYSTEM USERS TABLE -->
<div class="card-custom">
    <div class="card-custom-header">
        <h5 class="card-custom-title"><i class="bi bi-people text-primary"></i> Portal User Accounts & Assigned Roles</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email Address</th>
                    <th>Phone</th>
                    <th>Current Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th class="text-end">Change Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    {{ substr($u->name, 0, 1) }}
                                </div>
                                <div class="fw-bold text-dark">{{ $u->name }}</div>
                            </div>
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->phone ?? '-' }}</td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">
                                {{ $u->role->name ?? 'None' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $u->status === 'Active' ? 'badge-active' : 'bg-secondary' }}">
                                {{ $u->status }}
                            </span>
                        </td>
                        <td class="text-xs text-muted">
                            {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>

                            <!-- Edit User Modal -->
                            <div class="modal fade text-start" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('users.updateRole', $u->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Update Role for {{ $u->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Assign Role <span class="text-danger">*</span></label>
                                                    <select name="role_id" class="form-select" required>
                                                        @foreach($roles as $r)
                                                            <option value="{{ $r->id }}" {{ $u->role_id == $r->id ? 'selected' : '' }}>
                                                                {{ $r->name }} ({{ $r->description }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Account Status <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="Active" {{ $u->status === 'Active' ? 'selected' : '' }}>Active</option>
                                                        <option value="Inactive" {{ $u->status === 'Inactive' ? 'selected' : '' }}>Inactive / Suspended</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-mg-primary">Save Changes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $users->links() }}
        </div>
    @endif
</div>

<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus text-primary me-2"></i> Add Portal User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="User's complete name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="user@medhaversum.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="+91 XXXXX XXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign Role <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->description }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Initial Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimum 8 characters" value="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-mg-gold px-4">Create User</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
