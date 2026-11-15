<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Student Profile - {{ $student->student_id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1E293B;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #C59B27;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo-table {
            width: 100%;
        }
        .institute-title {
            font-size: 20px;
            font-weight: bold;
            color: #0B1B3D;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .institute-sub {
            font-size: 10px;
            color: #C59B27;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .institute-desc {
            font-size: 9px;
            color: #64748B;
        }
        .profile-title {
            background-color: #0B1B3D;
            color: #FFFFFF;
            padding: 6px 12px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #E2E8F0;
            padding: 7px 10px;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #F8FAFC;
            color: #475569;
            font-weight: bold;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            background-color: #DCFCE7;
            color: #166534;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #CBD5E1;
            padding-top: 10px;
            font-size: 9px;
            color: #94A3B8;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="logo-table">
            <tr>
                <td style="width: 75%;">
                    <div class="institute-title">MEDHAVERSUM GURUKULAM</div>
                    <div class="institute-sub">A Universe of Infinite Potential</div>
                    <div class="institute-desc">Mind Science, Neuro-Cognitive Activation & Gurukulam Management Portal</div>
                </td>
                <td style="width: 25%; text-align: right;">
                    <div style="font-size: 13px; font-weight: bold; color: #0B1B3D;">STUDENT DOSSIER</div>
                    <div style="font-size: 10px; color: #64748B;">Date: {{ date('d M Y') }}</div>
                    <div style="font-size: 11px; font-weight: bold; color: #1E40AF; margin-top: 4px;">{{ $student->student_id }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Personal & Parent Info -->
    <div class="profile-title">1. Student & Parent Information</div>
    <table class="data-table">
        <tr>
            <th style="width: 25%;">Student Full Name</th>
            <td style="width: 25%; font-weight: bold;">{{ $student->full_name }}</td>
            <th style="width: 25%;">Admission Status</th>
            <td style="width: 25%;"><span class="badge">{{ $student->status }}</span></td>
        </tr>
        <tr>
            <th>Date of Birth</th>
            <td>{{ $student->dob ? $student->dob->format('d M Y') : 'N/A' }}</td>
            <th>Gender</th>
            <td>{{ $student->gender }}</td>
        </tr>
        <tr>
            <th>Mobile Number</th>
            <td>{{ $student->mobile }}</td>
            <th>Email</th>
            <td>{{ $student->email ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Parent / Guardian</th>
            <td style="font-weight: bold;">{{ $student->parent_name }} ({{ $student->parent_relation }})</td>
            <th>Parent Contact</th>
            <td>{{ $student->parent_contact }}</td>
        </tr>
        <tr>
            <th>Residential Address</th>
            <td colspan="3">{{ $student->address ?? 'N/A' }}, {{ $student->city }}, {{ $student->state }} - {{ $student->pincode }}</td>
        </tr>
    </table>

    <!-- Courses & Batches -->
    <div class="profile-title">2. Academic Programs & Batches</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Course Code</th>
                <th>Batch</th>
                <th>Admission Date</th>
                <th>Total Course Fee</th>
            </tr>
        </thead>
        <tbody>
            @foreach($student->enrollments as $enr)
                <tr>
                    <td style="font-weight: bold;">{{ $enr->course->name ?? 'Course' }}</td>
                    <td>{{ $enr->course->course_code ?? '' }}</td>
                    <td>{{ $enr->batch->batch_name ?? 'Regular Batch' }}</td>
                    <td>{{ $enr->enrollment_date ? \Carbon\Carbon::parse($enr->enrollment_date)->format('d M Y') : 'N/A' }}</td>
                    <td style="font-weight: bold;">INR {{ number_format($enr->final_payable_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Financial Status -->
    <div class="profile-title">3. Fee Ledger & Payments Summary</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Total Course Fee</th>
                <th>Total Paid</th>
                <th>Outstanding Balance</th>
                <th>Fee Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold;">INR {{ number_format($student->fees->sum('net_amount'), 2) }}</td>
                <td style="font-weight: bold; color: #166534;">INR {{ number_format($student->total_paid, 2) }}</td>
                <td style="font-weight: bold; color: #991B1B;">INR {{ number_format($student->total_pending, 2) }}</td>
                <td><span class="badge">{{ $student->fees->first()->fee_status ?? 'Pending' }}</span></td>
            </tr>
        </tbody>
    </table>

    <!-- Recent Payments -->
    <div style="font-weight: bold; margin-bottom: 6px; font-size: 10px; color: #475569;">Payment Transactions:</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Receipt #</th>
                <th>Payment Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @forelse($student->payments as $pm)
                <tr>
                    <td style="font-weight: bold;">{{ $pm->receipt_no }}</td>
                    <td>{{ $pm->payment_date ? $pm->payment_date->format('d M Y') : 'N/A' }}</td>
                    <td style="font-weight: bold;">INR {{ number_format($pm->amount, 2) }}</td>
                    <td>{{ $pm->payment_method }}</td>
                    <td>{{ $pm->transaction_reference ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align: center;">No transactions on record.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated automatically by Medhaversum Gurukulam Management System &bull; Official Digital Record &bull; Verification available via Admin Portal
    </div>
</body>
</html>
