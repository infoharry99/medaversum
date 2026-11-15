@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark m-0">System Alerts & Reminders</h4>
        <span class="text-muted small">Upcoming fee dues, pending follow-ups, batch starts, and attendance notices</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('notifications.readAll') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="bi bi-check2-all me-1"></i> Mark All as Read
        </a>
    </div>
</div>

<div class="card-custom">
    <div class="card-custom-body p-0">
        <div class="list-group list-group-flush">
            @forelse($notifications as $notif)
                <div class="list-group-item p-3 d-flex align-items-start justify-content-between gap-3 {{ $notif->is_read ? 'opacity-75' : 'bg-light bg-opacity-25' }}">
                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary fs-5">
                            <i class="bi {{ $notif->icon ?? 'bi-bell' }}"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="fw-bold text-dark m-0">{{ $notif->title }}</h6>
                                <span class="badge bg-light text-secondary border text-xs" style="font-size: 0.7rem;">{{ $notif->type }}</span>
                                @if(!$notif->is_read)
                                    <span class="badge bg-danger rounded-pill" style="font-size: 0.6rem;">Unread</span>
                                @endif
                            </div>
                            <p class="text-secondary small mb-1">{{ $notif->message }}</p>
                            <span class="text-xs text-muted" style="font-size: 0.72rem;">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($notif->link)
                            <a href="{{ route('notifications.markRead', $notif->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                View Action &rarr;
                            </a>
                        @endif
                        @if(!$notif->is_read)
                            <a href="{{ route('notifications.markRead', $notif->id) }}" class="btn btn-sm btn-light border text-muted" title="Mark Read">
                                <i class="bi bi-check2"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bell-slash fs-1 text-muted mb-2 d-block"></i>
                    No notifications or pending alerts at this moment.
                </div>
            @endforelse
        </div>
    </div>
    @if($notifications->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
