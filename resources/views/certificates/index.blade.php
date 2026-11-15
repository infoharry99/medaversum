@extends('layouts.app')

@section('title', 'Certificates')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark m-0">Certificate Management</h4>
        <span class="text-muted small">Issue course completion certificates, generate verifiable PDF credentials, and validate codes</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-mg-gold btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#issueCertModal">
            <i class="bi bi-award-fill me-1"></i> Issue Certificate
        </button>
    </div>
</div>

<!-- CERTIFICATES TABLE -->
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Certificate No</th>
                    <th>Student Name</th>
                    <th>Course Completed</th>
                    <th>Completion Date</th>
                    <th>Grade / Distinction</th>
                    <th>Verification Code</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificates as $cert)
                    <tr>
                        <td><span class="badge bg-light text-primary border fw-bold">{{ $cert->certificate_number }}</span></td>
                        <td>
                            <a href="{{ route('students.show', $cert->student_id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $cert->student->full_name ?? 'N/A' }}
                            </a>
                            <div class="text-xs text-muted">{{ $cert->student->student_id ?? '' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $cert->course->name ?? 'Course' }}</div>
                        </td>
                        <td>{{ $cert->completion_date ? $cert->completion_date->format('d M Y') : 'N/A' }}</td>
                        <td><span class="badge bg-warning bg-opacity-10 text-dark fw-bold border">{{ $cert->grade_score }}</span></td>
                        <td><code>{{ $cert->verification_code }}</code></td>
                        <td><span class="badge badge-active">{{ $cert->status }}</span></td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('certificates.download', $cert->id) }}" class="btn btn-sm btn-outline-danger" title="Download Official PDF Certificate">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                                <a href="{{ route('certificates.verify', $cert->verification_code) }}" target="_blank" class="btn btn-sm btn-light border" title="Verify Online">
                                    <i class="bi bi-shield-check"></i> Verify
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No certificates issued yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($certificates->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $certificates->links() }}
        </div>
    @endif
</div>

<!-- ISSUE CERTIFICATE MODAL -->
<div class="modal fade" id="issueCertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('certificates.generate') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-award text-warning me-2"></i> Issue Course Certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Student <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Choose Student</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->student_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select" required>
                            <option value="">Choose Completed Course</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->course_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Completion Date <span class="text-danger">*</span></label>
                            <input type="date" name="completion_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Grade / Distinction <span class="text-danger">*</span></label>
                            <select name="grade_score" class="form-select" required>
                                <option value="Grade A+ (Exemplary Mastery)" selected>Grade A+ (Exemplary Mastery)</option>
                                <option value="Grade A (Distinction)">Grade A (Distinction)</option>
                                <option value="Grade B+ (First Class)">Grade B+ (First Class)</option>
                                <option value="Certificate of Completion">Certificate of Completion</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Authorized Signatory <span class="text-danger">*</span></label>
                        <input type="text" name="authorized_signatory" class="form-control" value="Dr. Madhav Sharma (Founder & Director)" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-mg-gold px-4">Generate & Download Certificate</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
