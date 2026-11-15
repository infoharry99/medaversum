@extends('layouts.app')

@section('title', 'Media Library')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark m-0">Centralized Media Library</h4>
        <span class="text-muted small">Course collateral, event highlights, student memory demonstrations, and classroom media</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-mg-gold btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Media
        </button>
    </div>
</div>

<!-- CATEGORY PILLS -->
<div class="d-flex flex-wrap gap-2 mb-4">
    @foreach($categories as $catName => $catCount)
        <a href="{{ route('media.index', $catName === 'All' ? [] : ['category' => $catName]) }}" class="btn btn-sm rounded-pill {{ (request('category') === $catName || (empty(request('category')) && $catName === 'All')) ? 'btn-mg-primary' : 'btn-light border' }} px-3">
            {{ $catName }} <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $catCount }}</span>
        </a>
    @endforeach
</div>

<!-- MEDIA GRID -->
<div class="row g-4">
    @forelse($mediaFiles as $media)
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="card-custom h-100 shadow-sm border">
                <div class="position-relative bg-light d-flex align-items-center justify-content-center overflow-hidden" style="height: 160px;">
                    @if($media->file_type === 'image')
                        <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->title }}" class="w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('assets/images/logo.png') }}';">
                    @elseif($media->file_type === 'video')
                        <div class="text-center p-3">
                            <i class="bi bi-play-circle-fill text-danger" style="font-size: 3rem;"></i>
                            <div class="text-xs text-muted mt-1">Video Recording</div>
                        </div>
                    @else
                        <div class="text-center p-3">
                            <i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size: 3rem;"></i>
                            <div class="text-xs text-muted mt-1">Course Material</div>
                        </div>
                    @endif
                    <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white" style="font-size: 0.7rem;">
                        {{ $media->category }}
                    </span>
                </div>
                <div class="card-body p-3">
                    <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $media->title }}">{{ $media->title }}</h6>
                    <div class="text-xs text-muted mb-2">
                        @if($media->course)
                            <span><i class="bi bi-journal"></i> {{ $media->course->name }}</span>
                        @else
                            <span>Gurukulam General</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="text-xs text-muted">{{ round($media->file_size / 1024, 1) }} KB</span>
                        <div class="btn-group">
                            <a href="{{ asset('storage/' . $media->file_path) }}" target="_blank" class="btn btn-sm btn-light border" title="Preview / Download">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            <form action="{{ route('media.destroy', $media->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light border text-danger btn-delete-confirm" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-images text-muted" style="font-size: 3.5rem;"></i>
            <h5 class="fw-bold text-dark mt-3">No Media Files In This Category</h5>
            <p class="text-muted small">Upload photos, videos, or course documents using the button above.</p>
        </div>
    @endforelse
</div>

@if($mediaFiles->hasPages())
    <div class="p-3 mt-4 border-top d-flex justify-content-end">
        {{ $mediaFiles->links() }}
    </div>
@endif

<!-- UPLOAD MEDIA MODAL -->
<div class="modal fade" id="uploadMediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('media.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cloud-arrow-up text-primary me-2"></i> Upload to Media Library</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Media Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Batch 01 Mind Activation Workshop">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="Student Photos">Student Photos</option>
                            <option value="Student Videos">Student Videos</option>
                            <option value="Classroom Activities" selected>Classroom Activities</option>
                            <option value="Events">Events</option>
                            <option value="Workshops">Workshops</option>
                            <option value="Certificates">Certificates</option>
                            <option value="Course Materials">Course Materials</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Linked Course</label>
                            <select name="course_id" class="form-select">
                                <option value="">None / General</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Linked Batch</label>
                            <select name="batch_id" class="form-select">
                                <option value="">None</option>
                                @foreach($batches as $b)
                                    <option value="{{ $b->id }}">{{ $b->batch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Choose File (Image, Video, Document) <span class="text-danger">*</span></label>
                        <input type="file" name="media_file" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-mg-gold px-4">Upload File</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
