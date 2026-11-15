<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receipt - {{ $payment->receipt_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1E293B;
            margin: 0;
            padding: 24px;
            font-size: 12px;
            line-height: 1.5;
        }
        .receipt-box {
            border: 2px solid #C59B27;
            padding: 24px;
            border-radius: 8px;
            position: relative;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0B1B3D;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #0B1B3D;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .subtitle {
            font-size: 11px;
            color: #C59B27;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .receipt-tag {
            background-color: #0B1B3D;
            color: #FFFFFF;
            font-size: 13px;
            font-weight: bold;
            padding: 6px 14px;
            text-align: right;
            border-radius: 4px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .details-table th, .details-table td {
            border: 1px solid #E2E8F0;
            padding: 10px 14px;
        }
        .details-table th {
            background-color: #F8FAFC;
            color: #475569;
            text-transform: uppercase;
            font-size: 10px;
            text-align: left;
        }
        .amount-box {
            background-color: #F8FAFC;
            border: 1px dashed #CBD5E1;
            padding: 12px 16px;
            margin-bottom: 24px;
            border-radius: 6px;
        }
        .amount-large {
            font-size: 18px;
            font-weight: bold;
            color: #166534;
        }
        .sign-table {
            width: 100%;
            margin-top: 40px;
        }
        .sign-line {
            border-top: 1px solid #94A3B8;
            padding-top: 6px;
            font-size: 11px;
            color: #475569;
            text-align: center;
        }
        .watermark {
            position: absolute;
            top: 45%;
            left: 25%;
            font-size: 55px;
            color: rgba(197, 155, 39, 0.06);
            transform: rotate(-30deg);
            font-weight: bold;
            letter-spacing: 8px;
            text-transform: uppercase;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="receipt-box">
        <div class="watermark">MEDHAVERSUM</div>

        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td style="width: 70%;">
                    <div class="title">{{ $settingInstitute }}</div>
                    <div class="subtitle">{{ $settingTagline }}</div>
                    <div style="font-size: 10px; color: #64748B; margin-top: 4px;">
                        {{ $settingAddress }} &bull; Ph: {{ $settingPhone }} &bull; {{ $settingEmail }}
                    </div>
                    @if($settingGst)
                        <div style="font-size: 10px; color: #64748B;">GSTIN: {{ $settingGst }}</div>
                    @endif
                </td>
                <td style="width: 30%; text-align: right; vertical-align: top;">
                    <div class="receipt-tag">FEE RECEIPT</div>
                    <div style="font-size: 12px; font-weight: bold; color: #0B1B3D; margin-top: 6px;">{{ $payment->receipt_no }}</div>
                    <div style="font-size: 10px; color: #64748B;">Date: {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : date('d M Y') }}</div>
                </td>
            </tr>
        </table>

        <!-- STUDENT & TRANSACTION DETAILS -->
        <table class="details-table">
            <tr>
                <th style="width: 25%;">Student ID</th>
                <td style="width: 25%; font-weight: bold; color: #1E40AF;">{{ $payment->student->student_id ?? 'N/A' }}</td>
                <th style="width: 25%;">Payment Code</th>
                <td style="width: 25%;">{{ $payment->payment_code }}</td>
            </tr>
            <tr>
                <th>Student Full Name</th>
                <td style="font-weight: bold;">{{ $payment->student->full_name ?? 'N/A' }}</td>
                <th>Parent Name</th>
                <td>{{ $payment->student->parent_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Enrolled Course</th>
                <td colspan="3" style="font-weight: bold; color: #0B1B3D;">
                    {{ $payment->studentFee->studentCourse->course->name ?? ($payment->student->courses->first()->name ?? 'Gurukulam Program') }}
                </td>
            </tr>
            <tr>
                <th>Payment Mode</th>
                <td><strong>{{ $payment->payment_method }}</strong></td>
                <th>Transaction / Ref No</th>
                <td>{{ $payment->transaction_reference ?? 'None / Cash' }}</td>
            </tr>
            <tr>
                <th>Payment Remarks</th>
                <td colspan="3">{{ $payment->remarks ?? 'Course Fee Payment' }}</td>
            </tr>
        </table>

        <!-- AMOUNT SUMMARY -->
        <div class="amount-box">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60%;">
                        <div style="font-size: 11px; color: #64748B; text-transform: uppercase;">Amount Paid (INR):</div>
                        <div class="amount-large">₹ {{ number_format($payment->amount, 2) }}</div>
                        <div style="font-size: 10px; color: #475569; margin-top: 4px;">Status: <strong>{{ $payment->status }}</strong></div>
                    </td>
                    <td style="width: 40%; text-align: right; border-left: 1px solid #CBD5E1; padding-left: 14px;">
                        <div style="font-size: 10px; color: #64748B;">Course Net Fee: <strong>₹ {{ number_format($payment->studentFee->net_amount ?? 0, 2) }}</strong></div>
                        <div style="font-size: 10px; color: #166534;">Total Paid So Far: <strong>₹ {{ number_format($payment->studentFee->paid_amount ?? 0, 2) }}</strong></div>
                        <div style="font-size: 11px; color: #991B1B; font-weight: bold; margin-top: 4px;">
                            Remaining Balance: ₹ {{ number_format($payment->studentFee->pending_amount ?? 0, 2) }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- SIGNATURES -->
        <table class="sign-table">
            <tr>
                <td style="width: 50%; text-align: left;">
                    <div style="font-size: 10px; color: #64748B;">Recorded By:</div>
                    <div style="font-weight: bold; font-size: 11px;">{{ $payment->recordedBy->name ?? 'Accounts Officer' }}</div>
                    <div style="font-size: 9px; color: #94A3B8;">System Certified Timestamp: {{ $payment->created_at->format('d M Y, h:i A') }}</div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div style="width: 200px; display: inline-block;">
                        <div style="height: 35px;"></div>
                        <div class="sign-line">Authorized Signatory & Seal<br><span style="font-size: 9px;">Medhaversum Gurukulam</span></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
