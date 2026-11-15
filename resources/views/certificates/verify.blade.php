<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification - Medhaversum Gurukulam</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Cinzel:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #071126 0%, #0B1B3D 60%, #162a56 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .cert-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4);
            border: 2px solid #C59B27;
            width: 100%;
            max-width: 580px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="cert-card p-4 p-md-5">
        <div class="text-center mb-4">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;">
            <h4 class="fw-bold text-dark mt-2 mb-0" style="font-family: 'Cinzel', serif;">MEDHAVERSUM GURUKULAM</h4>
            <div class="text-warning small fw-bold">Official Credential Verification</div>
        </div>

        @if($certificate)
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-3 p-3 rounded-3 mb-4">
                <i class="bi bi-patch-check-fill fs-1 text-success"></i>
                <div>
                    <h6 class="fw-bold text-success mb-1">Authentic & Verified Certificate</h6>
                    <span class="text-xs text-muted">This credential was officially issued by Medhaversum Gurukulam.</span>
                </div>
            </div>

            <table class="table table-bordered mb-4">
                <tbody>
                    <tr><th class="table-light" style="width: 40%;">Student Name</th><td class="fw-bold fs-6 text-dark">{{ $certificate->student->full_name }}</td></tr>
                    <tr><th class="table-light">Student ID</th><td class="fw-bold text-primary">{{ $certificate->student->student_id }}</td></tr>
                    <tr><th class="table-light">Course Completed</th><td class="fw-bold text-dark">{{ $certificate->course->name }}</td></tr>
                    <tr><th class="table-light">Certificate Number</th><td><span class="badge bg-light text-dark border">{{ $certificate->certificate_number }}</span></td></tr>
                    <tr><th class="table-light">Distinction / Grade</th><td class="fw-bold text-success">{{ $certificate->grade_score }}</td></tr>
                    <tr><th class="table-light">Completion Date</th><td>{{ $certificate->completion_date ? $certificate->completion_date->format('d F Y') : 'N/A' }}</td></tr>
                    <tr><th class="table-light">Issued By</th><td>{{ $certificate->authorized_signatory }}</td></tr>
                    <tr><th class="table-light">Verification Code</th><td><code>{{ $certificate->verification_code }}</code></td></tr>
                </tbody>
            </table>

            <div class="text-center">
                <a href="{{ route('certificates.download', $certificate->id) }}" class="btn btn-outline-danger btn-sm rounded-pill px-4">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF Copy
                </a>
            </div>
        @else
            <div class="alert alert-danger border-0 shadow-sm text-center p-4 rounded-3 mb-4">
                <i class="bi bi-exclamation-triangle-fill fs-1 text-danger mb-2 d-block"></i>
                <h5 class="fw-bold text-danger">Invalid or Unverified Credential</h5>
                <p class="small text-muted mb-0">No record exists for verification code: <code>{{ $code }}</code>. Please check the code and try again.</p>
            </div>
        @endif

        <div class="text-center mt-4 pt-3 border-top small text-muted">
            &copy; {{ date('Y') }} Medhaversum Gurukulam. A Universe of Infinite Potential.
        </div>
    </div>
</body>
</html>
