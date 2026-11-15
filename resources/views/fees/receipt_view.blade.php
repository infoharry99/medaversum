@extends('layouts.app')

@section('title', 'Receipt: ' . $payment->receipt_no)

@section('content')
<div class="mb-4">
    <a href="{{ route('fees.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Fees</a>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark m-0">Payment Receipt: {{ $payment->receipt_no }}</h4>
            <span class="text-muted small">Official payment acknowledgment for student {{ $payment->student->full_name }}</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-printer me-1"></i> Print Receipt
            </button>
            <a href="{{ route('fees.receiptPdf', $payment->id) }}" class="btn btn-mg-gold btn-sm rounded-pill px-3">
                <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4 shadow-sm" style="border: 2px solid #C59B27;">
            <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="width: 70px; height: 70px; object-fit: contain;">
                    <div>
                        <h4 class="fw-bold text-dark m-0" style="font-family: 'Cinzel', serif;">MEDHAVERSUM GURUKULAM</h4>
                        <div class="text-warning small fw-bold">A Universe of Infinite Potential</div>
                        <div class="text-muted small">Mind Science & Neuro-Cognitive Gurukulam Campus &bull; New Delhi</div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-dark text-white fs-6 px-3 py-2 rounded-3">OFFICIAL RECEIPT</span>
                    <div class="fw-bold text-primary fs-5 mt-2">{{ $payment->receipt_no }}</div>
                    <div class="text-muted small">Date: {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 'N/A' }}</div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Student Information</h6>
                        <div class="fw-bold fs-6 text-dark">{{ $payment->student->full_name }}</div>
                        <div class="text-xs text-muted">Student ID: <strong>{{ $payment->student->student_id }}</strong></div>
                        <div class="text-xs text-muted">Parent: {{ $payment->student->parent_name }} ({{ $payment->student->mobile }})</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Transaction Details</h6>
                        <div>Payment Method: <strong>{{ $payment->payment_method }}</strong></div>
                        <div>Reference / UTR: <strong>{{ $payment->transaction_reference ?? 'N/A' }}</strong></div>
                        <div class="text-xs text-muted">Status: <span class="badge badge-paid">{{ $payment->status }}</span></div>
                    </div>
                </div>
            </div>

            <table class="table table-bordered mb-4">
                <thead class="table-light">
                    <tr>
                        <th>Description / Course</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $payment->studentFee->studentCourse->course->name ?? 'Course Fee Payment' }}</div>
                            <div class="text-muted small">{{ $payment->remarks ?? 'Tuition fee installment payment' }}</div>
                        </td>
                        <td class="text-end fw-bold text-success fs-5">
                            ₹{{ number_format($payment->amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="p-3 bg-light rounded-3 mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block">Total Course Fee: ₹{{ number_format($payment->studentFee->net_amount ?? 0, 2) }}</span>
                    <span class="text-muted small d-block">Total Paid to Date: ₹{{ number_format($payment->studentFee->paid_amount ?? 0, 2) }}</span>
                </div>
                <div class="text-end">
                    <span class="text-muted small d-block">Remaining Balance Due</span>
                    <span class="fw-bold fs-5 text-danger">₹{{ number_format($payment->studentFee->pending_amount ?? 0, 2) }}</span>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-end pt-4 border-top">
                <div class="small text-muted">
                    Recorded by: <strong>{{ $payment->recordedBy->name ?? 'Accounts Officer' }}</strong><br>
                    Computer generated verifiable receipt.
                </div>
                <div class="text-center" style="width: 200px;">
                    <div class="border-top pt-2 small fw-semibold text-dark">
                        Authorized Signatory<br>
                        <span class="text-xs text-muted">Medhaversum Gurukulam</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
