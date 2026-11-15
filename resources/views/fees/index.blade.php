@extends('layouts.app')

@section('title', 'Fees & Payments')

@section('content')
<!-- TOP FILTER BAR MATCHING REFERENCE UI -->
<div class="card-custom mb-3">
    <div class="p-3">
        <form action="{{ route('fees.index') }}" method="GET" id="feeFilterForm">
            <!-- Filter Fields Grid -->
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="filter-label">SEARCH STUDENT / RECEIPT</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 py-1 px-2 text-muted" style="font-size: 0.85rem;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control filter-control border-start-0 ps-1" 
                               placeholder="Student name, student ID, mobile or receipt no..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-4">
                    <label class="filter-label">FEE STATUS</label>
                    <select name="fee_status" class="form-select filter-control">
                        <option value="">All Fee Statuses</option>
                        <option value="Paid" {{ request('fee_status') === 'Paid' ? 'selected' : '' }}>Paid in Full</option>
                        <option value="Partial" {{ request('fee_status') === 'Partial' ? 'selected' : '' }}>Partial (Balance Due)</option>
                        <option value="Pending" {{ request('fee_status') === 'Pending' ? 'selected' : '' }}>Pending (Unpaid)</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex justify-content-end align-items-center gap-2">
                    <a href="{{ route('fees.index') }}" class="btn-filter-reset">Reset</a>
                    <button type="submit" class="btn-filter-apply">
                        <i class="bi bi-funnel-fill"></i> Apply Filters
                    </button>
                </div>
            </div>

            <!-- Sub Filter Bar: Action Buttons & Record Payment -->
            <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 mt-3 border-top gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('reports.index') }}" class="btn-header-secondary">
                        <i class="bi bi-file-earmark-bar-graph"></i> Financial Reports
                    </a>
                    <a href="{{ route('reports.index') }}" class="btn-header-secondary">
                        <i class="bi bi-calendar3"></i> Collection Summary
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn-header-primary" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                        <i class="bi bi-wallet2"></i> + Record Fee Payment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- FINANCIAL KPI METRIC CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Total Payable</h6>
                <h3>₹{{ number_format($totalPayable, 2) }}</h3>
                <span class="small text-muted">Net committed course fees</span>
            </div>
            <div class="stat-card-icon icon-navy"><i class="bi bi-briefcase"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Total Collected</h6>
                <h3 class="text-success">₹{{ number_format($totalCollected, 2) }}</h3>
                <span class="small text-success fw-semibold"><i class="bi bi-check-circle"></i> Successfully cleared</span>
            </div>
            <div class="stat-card-icon icon-green"><i class="bi bi-cash-stack"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Pending Balance</h6>
                <h3 class="text-danger">₹{{ number_format($totalPending, 2) }}</h3>
                <span class="small text-danger fw-semibold"><i class="bi bi-exclamation-circle"></i> Outstanding dues</span>
            </div>
            <div class="stat-card-icon icon-red"><i class="bi bi-hourglass-split"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Today's Collections</h6>
                <h3 style="color: #854D0E;">₹{{ number_format($todayCollected, 2) }}</h3>
                <span class="small text-muted">{{ date('d M Y') }}</span>
            </div>
            <div class="stat-card-icon icon-gold"><i class="bi bi-calendar2-check"></i></div>
        </div>
    </div>
</div>

<!-- STUDENT FEES LEDGER CARD -->
<div class="card-custom mb-4">
    <div class="card-custom-header">
        <div class="page-title-box">
            <h5 class="page-main-heading">
                Student Fee Ledgers
            </h5>
            <span class="page-sub-heading">
                All registered student accounts, course commitments, collected funds, and pending installment balances
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">S.no <span class="sort-arrow">⇅</span></th>
                    <th class="active-col-header" style="width: 130px;">Student ID <span class="sort-arrow">⇅</span></th>
                    <th>Student Name <span class="sort-arrow">⇅</span></th>
                    <th>Course <span class="sort-arrow">⇅</span></th>
                    <th>Net Fee <span class="sort-arrow">⇅</span></th>
                    <th>Paid Amount <span class="sort-arrow">⇅</span></th>
                    <th>Pending Balance <span class="sort-arrow">⇅</span></th>
                    <th>Status <span class="sort-arrow">⇅</span></th>
                    <th class="text-end" style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $avatarColors = ['avatar-green', 'avatar-teal', 'avatar-blue', 'avatar-purple', 'avatar-amber'];
                @endphp
                @forelse($fees as $index => $f)
                    @php
                        $avatarClass = $avatarColors[$index % count($avatarColors)];
                        $initials = collect(explode(' ', $f->student->full_name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                        $firstCourse = $f->student->courses->first();
                    @endphp
                    <tr>
                        <!-- S.no -->
                        <td class="text-muted fw-semibold">
                            {{ $fees->firstItem() + $index }}
                        </td>

                        <!-- Student ID -->
                        <td>
                            <a href="{{ route('students.show', $f->student_id) }}" class="text-decoration-none">
                                <span class="badge-code-pill">{{ $f->student->student_id }}</span>
                            </a>
                        </td>

                        <!-- Student Name -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initials {{ $avatarClass }}">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <a href="{{ route('students.show', $f->student_id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $f->student->full_name }}
                                    </a>
                                    <div class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $f->student->mobile }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Course (Yellow Tag) -->
                        <td>
                            @if($firstCourse)
                                <div class="badge-category-tag">
                                    <span>{{ $firstCourse->name }}</span>
                                    <span class="badge-sub-chart">{{ $firstCourse->course_code }}</span>
                                </div>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>

                        <!-- Net Fee -->
                        <td>
                            <span class="fw-bold text-dark font-monospace" style="font-size: 0.85rem;">
                                ₹{{ number_format($f->net_amount, 2) }}
                            </span>
                        </td>

                        <!-- Paid Amount -->
                        <td>
                            <span class="fw-bold text-success font-monospace" style="font-size: 0.85rem;">
                                ₹{{ number_format($f->paid_amount, 2) }}
                            </span>
                        </td>

                        <!-- Pending Balance -->
                        <td>
                            <span class="fw-bold {{ $f->pending_amount > 0 ? 'text-danger' : 'text-muted' }} font-monospace" style="font-size: 0.85rem;">
                                ₹{{ number_format($f->pending_amount, 2) }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td>
                            @php
                                $statusBadge = match($f->fee_status) {
                                    'Paid' => 'bg-success text-white',
                                    'Partial' => 'badge-status-pending',
                                    default => 'bg-danger text-white',
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }}" style="font-size: 0.72rem; padding: 4px 8px;">
                                {{ strtoupper($f->fee_status) }}
                            </span>
                        </td>

                        <!-- Collect Action -->
                        <td class="text-end">
                            <button class="btn btn-sm btn-header-primary py-1 px-2 btn-collect-direct" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#collectPaymentModal" 
                                    data-fee-id="{{ $f->id }}" 
                                    data-student-name="{{ $f->student->full_name }}" 
                                    data-pending="{{ $f->pending_amount }}"
                                    style="font-size: 0.76rem;">
                                <i class="bi bi-cash me-1"></i> Collect
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No student fee ledgers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($fees->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center bg-white">
            <span class="text-muted small">
                Showing {{ $fees->firstItem() }} to {{ $fees->lastItem() }} of {{ $fees->total() }} accounts
            </span>
            <div>
                {{ $fees->links() }}
            </div>
        </div>
    @endif
</div>

<!-- RECENT RECEIPTS & AUDIT TRAIL CARD -->
<div class="card-custom">
    <div class="card-custom-header">
        <div class="page-title-box">
            <h5 class="page-main-heading">
                Payment Receipts & Audit History
            </h5>
            <span class="page-sub-heading">
                Immutable audit ledger, downloadable GST-compliant PDF receipts, and transaction adjustment logs
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">S.no <span class="sort-arrow">⇅</span></th>
                    <th class="active-col-header" style="width: 140px;">Receipt No <span class="sort-arrow">⇅</span></th>
                    <th>Payment Code <span class="sort-arrow">⇅</span></th>
                    <th>Student Name <span class="sort-arrow">⇅</span></th>
                    <th>Payment Date <span class="sort-arrow">⇅</span></th>
                    <th>Amount Paid <span class="sort-arrow">⇅</span></th>
                    <th>Method <span class="sort-arrow">⇅</span></th>
                    <th>Ref / UTR <span class="sort-arrow">⇅</span></th>
                    <th>Status <span class="sort-arrow">⇅</span></th>
                    <th class="text-end" style="width: 130px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPayments as $pIndex => $pm)
                    <tr>
                        <!-- S.no -->
                        <td class="text-muted fw-semibold">
                            {{ $loop->iteration }}
                        </td>

                        <!-- Receipt No -->
                        <td>
                            <a href="{{ route('fees.receiptView', $pm->id) }}" class="text-decoration-none">
                                <span class="badge-code-pill fw-bold text-primary">{{ $pm->receipt_no }}</span>
                            </a>
                        </td>

                        <!-- Payment Code -->
                        <td>
                            <span class="text-muted font-monospace" style="font-size: 0.74rem;">{{ $pm->payment_code }}</span>
                        </td>

                        <!-- Student Name -->
                        <td>
                            <div class="fw-semibold text-dark">{{ $pm->student->full_name ?? 'N/A' }}</div>
                            <div class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $pm->student->student_id ?? '' }}</div>
                        </td>

                        <!-- Payment Date -->
                        <td>
                            <span class="text-dark" style="font-size: 0.8rem;">
                                {{ $pm->payment_date ? $pm->payment_date->format('d M Y') : 'N/A' }}
                            </span>
                        </td>

                        <!-- Amount Paid -->
                        <td>
                            <span class="fw-bold text-success font-monospace" style="font-size: 0.85rem;">
                                ₹{{ number_format($pm->amount, 2) }}
                            </span>
                        </td>

                        <!-- Method -->
                        <td>
                            <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.72rem;">
                                {{ $pm->payment_method }}
                            </span>
                        </td>

                        <!-- Ref / UTR -->
                        <td>
                            <span class="text-muted font-monospace text-truncate d-inline-block" style="max-width: 110px; font-size: 0.72rem;">
                                {{ $pm->transaction_reference ?? '&mdash;' }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td>
                            <span class="badge {{ $pm->status === 'Successful' ? 'bg-success text-white' : 'bg-warning text-dark' }}" style="font-size: 0.72rem; padding: 4px 8px;">
                                {{ strtoupper($pm->status) }}
                            </span>
                            @if($pm->is_correction)
                                <span class="badge bg-danger text-white text-xs ms-1">Adjusted</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="{{ route('fees.receiptPdf', $pm->id) }}" class="action-icon-btn text-danger" title="Download Official PDF Receipt">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                <a href="{{ route('fees.receiptView', $pm->id) }}" class="action-icon-btn" title="View Web Receipt">
                                    <i class="bi bi-printer"></i>
                                </a>
                                @if($pm->status === 'Successful')
                                    <button class="action-icon-btn text-warning" title="Correct / Refund Payment" data-bs-toggle="modal" data-bs-target="#correctModal{{ $pm->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                @endif
                            </div>

                            <!-- Payment Correction Modal -->
                            <div class="modal fade" id="correctModal{{ $pm->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('fees.correctPayment', $pm->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content text-start">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Payment Audit & Adjustment</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-warning small">
                                                    Financial transactions are never permanently deleted. This records an immutable audit log and recalculates student balances.
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Receipt</label>
                                                    <input type="text" class="form-control bg-light" value="{{ $pm->receipt_no }} (₹{{ number_format($pm->amount, 2) }})" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Action Type <span class="text-danger">*</span></label>
                                                    <select name="action_type" class="form-select" required>
                                                        <option value="Refund">Refund to Student</option>
                                                        <option value="Correction">Data Correction / Adjustment</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Reason / Justification <span class="text-danger">*</span></label>
                                                    <textarea name="reason" rows="3" class="form-control" required placeholder="Mandatory audit trail reason"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Apply Adjustment</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center py-4 text-muted">No payment receipts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- RECORD FEE PAYMENT MODAL -->
<div class="modal fade" id="collectPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('fees.collect') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-wallet2 text-primary me-2"></i> Record Fee Collection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Student Ledger <span class="text-danger">*</span></label>
                        <select name="student_fee_id" id="modalStudentFeeId" class="form-select" required>
                            <option value="">Choose Student & Fee Record</option>
                            @foreach($students as $st)
                                @if($st->fees->isNotEmpty())
                                    @php $f = $st->fees->first(); @endphp
                                    <option value="{{ $f->id }}" data-pending="{{ $f->pending_amount }}">
                                        {{ $st->full_name }} ({{ $st->student_id }}) - Pending: ₹{{ number_format($f->pending_amount, 2) }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Amount Collected (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" id="modalAmount" class="form-control fw-bold text-success" required placeholder="e.g. 5000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="UPI">UPI / QR Code</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer (NEFT/RTGS)</option>
                                <option value="Card">Debit / Credit Card</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Transaction Ref / UTR</label>
                            <input type="text" name="transaction_reference" class="form-control" placeholder="UPI ref / Cheque no">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks / Notes</label>
                        <input type="text" name="remarks" class="form-control" placeholder="e.g. 2nd Installment payment">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-header-primary px-4">Generate Receipt & Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.btn-collect-direct', function() {
        const feeId = $(this).data('fee-id');
        const pending = $(this).data('pending');
        $('#modalStudentFeeId').val(feeId).change();
        $('#modalAmount').val(pending > 0 ? pending : '');
    });
</script>
@endpush
