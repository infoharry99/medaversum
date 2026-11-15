@extends('layouts.app')

@section('title', 'Role Permissions: ' . $role->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Roles</a>
    <h4 class="fw-bold text-dark m-0">Module Permissions: {{ $role->name }}</h4>
    <span class="text-muted small">Configure read, write, update and delete capabilities for role '{{ $role->name }}'</span>
</div>

<form action="{{ route('roles.updatePermissions', $role->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        @foreach($permissionsByModule as $moduleName => $perms)
            <div class="col-md-6 col-lg-4">
                <div class="card-custom h-100 shadow-sm border">
                    <div class="card-custom-header py-2 px-3 bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark small"><i class="bi bi-folder2 text-primary me-1"></i> {{ $moduleName }} Module</span>
                        <button type="button" class="btn btn-link p-0 text-decoration-none small text-primary select-all-module" data-module="{{ Str::slug($moduleName) }}">
                            Toggle All
                        </button>
                    </div>
                    <div class="card-body p-3">
                        @foreach($perms as $p)
                            <div class="form-check mb-2">
                                <input class="form-check-input perm-check mod-{{ Str::slug($moduleName) }}" type="checkbox" name="permissions[]" value="{{ $p->id }}" id="perm_{{ $p->id }}" {{ $role->permissions->contains('id', $p->id) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="perm_{{ $p->id }}">
                                    <span class="fw-semibold text-dark">{{ $p->name }}</span>
                                    <span class="text-xs text-muted d-block">{{ $p->slug }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card-custom mt-4">
        <div class="card-body p-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Changes take effect immediately across all users assigned to role <strong>{{ $role->name }}</strong>.</span>
            <div class="d-flex gap-2">
                <a href="{{ route('roles.index') }}" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-mg-gold px-4">Save Role Permissions</button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    $('.select-all-module').click(function() {
        const mod = $(this).data('module');
        const checks = $('.mod-' + mod);
        const allChecked = checks.length === checks.filter(':checked').length;
        checks.prop('checked', !allChecked);
    });
</script>
@endpush
