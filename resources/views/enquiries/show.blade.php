@extends('layouts.app')

@section('title', 'Enquiry: ' . $enquiry->enquiry_code)

@section('content')
<div class="mb-4">
    <a href="{{ route('enquiries.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Enquiries</a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h4 class="fw-bold text-dark m-0">{{ $enquiry->student_name }}</h4>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span class="badge bg-light text-primary border fw-bold">{{ $enquiry->enquiry_code }}</span>
                <span class="badge {{ $enquiry->status === 'Converted' ? 'badge-converted' : ($enquiry->status === 'Follow-up' ? 'badge-followup' : ($enquiry->status === 'Interested' ? 'bg-info text-white' : ($enquiry->status === 'New' ? 'badge-new' : 'bg-secondary'))) }}">
                    {{ $enquiry->status }}
                </span>
                <span class="text-muted small">&bull; Enquired on {{ \Carbon\Carbon::parse($enquiry->enquiry_date)->format('d M Y') }} via {{ $enquiry->source }}</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($enquiry->status !== 'Converted')
                <a href="{{ route('students.create') }}?enquiry_id={{ $enquiry->id }}" class="btn btn-mg-gold btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-person-check-fill me-1"></i> Convert to Admission
                </a>
            @else
                <span class="badge badge-converted px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Converted to Student</span>
            @endif
            <a href="{{ route('enquiries.edit', $enquiry->id) }}" class="btn btn-light border btn-sm rounded-pill px-3">
                <i class="bi bi-pencil me-1"></i> Edit Lead
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- LEFT: Lead Details -->
    <div class="col-lg-4">
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-person-lines-fill text-primary"></i> Lead Profile</h5>
            </div>
            <div class="card-custom-body p-3">
                <table class="table table-sm border-0 mb-0">
                    <tbody>
                        <tr><td class="text-muted">Student:</td><td class="fw-bold text-dark">{{ $enquiry->student_name }}</td></tr>
                        <tr><td class="text-muted">Parent / Guardian:</td><td class="fw-semibold">{{ $enquiry->parent_name ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted">Mobile:</td><td class="fw-semibold"><a href="tel:{{ $enquiry->mobile }}" class="text-decoration-none">{{ $enquiry->mobile }}</a></td></tr>
                        <tr><td class="text-muted">Email:</td><td>{{ $enquiry->email ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted">Interested Course:</td><td class="fw-bold text-primary">{{ $enquiry->course->name ?? 'General Inquiry' }}</td></tr>
                        <tr><td class="text-muted">Assigned Counselor:</td><td class="fw-semibold">{{ $enquiry->counselor->name ?? 'Unassigned' }}</td></tr>
                        <tr><td class="text-muted">Next Follow-up:</td><td class="text-danger fw-bold">{{ $enquiry->follow_up_date ? \Carbon\Carbon::parse($enquiry->follow_up_date)->format('d M Y') : 'None scheduled' }}</td></tr>
                        <tr><td class="text-muted">Source:</td><td><span class="badge bg-light text-dark border">{{ $enquiry->source }}</span></td></tr>
                        <tr><td class="text-muted">Remarks:</td><td class="small">{{ $enquiry->remarks ?? 'None' }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-lightning text-warning"></i> Interactions</h5>
            </div>
            <div class="card-custom-body p-3 d-grid gap-2">
                <button class="btn btn-outline-primary btn-sm py-2" data-bs-toggle="modal" data-bs-target="#counsellingModal">
                    <i class="bi bi-chat-quote-fill me-1"></i> Record Counselling Notes
                </button>
                <button class="btn btn-outline-secondary btn-sm py-2" data-bs-toggle="modal" data-bs-target="#followUpModal">
                    <i class="bi bi-telephone-plus-fill me-1"></i> Log Follow-up Call / Interaction
                </button>
            </div>
        </div>
    </div>

    <!-- RIGHT: Counselling & Follow-up History -->
    <div class="col-lg-8">
        <!-- Counselling Sessions -->
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-chat-dots-fill text-primary"></i> Counselling Sessions</h5>
                <button class="btn btn-sm btn-mg-primary" data-bs-toggle="modal" data-bs-target="#counsellingModal">+ New Session</button>
            </div>
            <div class="card-custom-body p-3">
                @forelse($enquiry->counsellingRecords as $rec)
                    <div class="p-3 border rounded-3 mb-3 bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge {{ $rec->student_interest_level === 'High' ? 'bg-success' : ($rec->student_interest_level === 'Medium' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    Interest: {{ $rec->student_interest_level }}
                                </span>
                                <span class="text-muted small ms-2"><i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($rec->session_date)->format('d M Y, h:i A') }}</span>
                            </div>
                            <span class="text-xs text-muted">Counselor: {{ $rec->counselor->name ?? 'Staff' }}</span>
                        </div>
                        <div class="small text-dark mb-2">
                            <strong>Discussion:</strong> {{ $rec->discussion_notes }}
                        </div>
                        @if($rec->recommendedCourse)
                            <div class="text-xs text-primary mb-1">
                                <i class="bi bi-check2"></i> Recommended Course: <strong>{{ $rec->recommendedCourse->name }}</strong>
                            </div>
                        @endif
                        @if($rec->next_step)
                            <div class="text-xs text-secondary">
                                <i class="bi bi-arrow-right"></i> Next Step: {{ $rec->next_step }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">No counselling notes recorded yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Follow-up Timeline -->
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-clock-history text-primary"></i> Follow-up Interactions Log</h5>
                <button class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#followUpModal">+ Log Follow-up</button>
            </div>
            <div class="card-custom-body p-3">
                @forelse($enquiry->followUps as $flp)
                    <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <div class="fw-bold text-dark small">{{ $flp->follow_up_type }} Follow-up</div>
                                <span class="text-xs text-muted">{{ \Carbon\Carbon::parse($flp->follow_up_date)->format('d M Y') }}</span>
                            </div>
                            <p class="small text-muted mb-1">{{ $flp->response_notes }}</p>
                            <div class="d-flex justify-content-between text-xs text-secondary">
                                <span>Logged by: {{ $flp->user->name ?? 'User' }}</span>
                                @if($flp->next_follow_up_date)
                                    <span class="text-danger fw-semibold">Next: {{ \Carbon\Carbon::parse($flp->next_follow_up_date)->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">No follow-ups logged yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Counselling Modal -->
<div class="modal fade" id="counsellingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('enquiries.addCounselling', $enquiry->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Record Counselling Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Session Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="session_date" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discussion Notes & Observations <span class="text-danger">*</span></label>
                        <textarea name="discussion_notes" rows="3" class="form-control" required placeholder="Detail student's challenges, attention span, parent expectations..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student Interest Level <span class="text-danger">*</span></label>
                        <select name="student_interest_level" class="form-select" required>
                            <option value="High">High - Ready to enroll</option>
                            <option value="Medium" selected>Medium - Considering options</option>
                            <option value="Low">Low - Exploring / undecided</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recommended Course</label>
                        <select name="recommended_course_id" class="form-select">
                            <option value="">Select Course</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ $enquiry->course_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Action / Next Step</label>
                        <input type="text" name="next_step" class="form-control" placeholder="e.g. Schedule demo class / send fee brochure">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-mg-gold">Save Notes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Follow-up Modal -->
<div class="modal fade" id="followUpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('enquiries.addFollowUp', $enquiry->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Log Follow-up Interaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Follow-up Date <span class="text-danger">*</span></label>
                            <input type="date" name="follow_up_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Interaction Medium <span class="text-danger">*</span></label>
                            <select name="follow_up_type" class="form-select" required>
                                <option value="Call">Phone Call</option>
                                <option value="WhatsApp">WhatsApp Message</option>
                                <option value="Meeting">In-Person Meeting</option>
                                <option value="Email">Email</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Response Notes <span class="text-danger">*</span></label>
                        <textarea name="response_notes" rows="3" class="form-control" required placeholder="What was discussed and parent's response?"></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Next Follow-up Date</label>
                            <input type="date" name="next_follow_up_date" class="form-control" value="{{ \Carbon\Carbon::now()->addDays(3)->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Interaction Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="Completed">Completed</option>
                                <option value="Rescheduled">Rescheduled</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-mg-gold">Log Interaction</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
