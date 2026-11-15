<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            margin: 0;
            padding: 30px;
            background-color: #FFFFFF;
            color: #0B1B3D;
            text-align: center;
        }
        .outer-border {
            border: 4px double #C59B27;
            padding: 24px;
            height: 90%;
            position: relative;
            background: #FFFFFF;
        }
        .inner-border {
            border: 1px solid #C59B27;
            padding: 20px 30px;
            height: 90%;
            position: relative;
        }
        .cert-header {
            font-size: 28px;
            font-weight: bold;
            color: #0B1B3D;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .cert-motto {
            font-size: 13px;
            color: #C59B27;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }
        .cert-title {
            font-size: 18px;
            color: #1E40AF;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 15px;
            font-style: italic;
        }
        .cert-presents {
            font-size: 13px;
            color: #64748B;
            margin-bottom: 10px;
        }
        .student-name {
            font-size: 32px;
            font-weight: bold;
            color: #0B1B3D;
            border-bottom: 2px solid #C59B27;
            display: inline-block;
            padding: 0 40px 6px;
            margin-bottom: 15px;
        }
        .cert-body {
            font-size: 14px;
            color: #334155;
            line-height: 1.6;
            max-width: 750px;
            margin: 0 auto 20px;
        }
        .course-title {
            font-size: 20px;
            font-weight: bold;
            color: #0B1B3D;
        }
        .footer-table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            text-align: center;
            width: 30%;
        }
        .signature-line {
            border-top: 1px solid #475569;
            padding-top: 5px;
            font-size: 11px;
            font-weight: bold;
        }
        .cert-meta {
            font-size: 9px;
            color: #94A3B8;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="outer-border">
        <div class="inner-border">
            <div class="cert-header">MEDHAVERSUM GURUKULAM</div>
            <div class="cert-motto">A UNIVERSE OF INFINITE POTENTIAL</div>

            <div class="cert-title">Certificate of Cognitive Excellence</div>
            <div class="cert-presents">This prestigious certification is proudly conferred upon</div>

            <div class="student-name">{{ $certificate->student->full_name }}</div>

            <div class="cert-body">
                having successfully completed the intensive neuro-cognitive development curriculum in
                <div class="course-title" style="margin: 8px 0;">{{ $certificate->course->name }}</div>
                demonstrating high mental discipline, photographic recall, and subconscious activation protocols with distinction grade of <strong>{{ $certificate->grade_score }}</strong> on <strong>{{ $certificate->completion_date ? $certificate->completion_date->format('F d, Y') : date('F d, Y') }}</strong>.
            </div>

            <!-- Signatures Table -->
            <table class="footer-table">
                <tr>
                    <td class="signature-box">
                        <div style="font-size: 10px; color: #64748B;">Date of Issue: {{ $certificate->issue_date ? $certificate->issue_date->format('d M Y') : date('d M Y') }}</div>
                        <div style="font-size: 10px; color: #1E40AF; font-weight: bold; margin-top: 3px;">ID: {{ $certificate->certificate_number }}</div>
                    </td>
                    <td class="signature-box">
                        <div style="height: 35px;"></div>
                        <div class="signature-line">
                            Acharya Anand Dev<br>
                            <span style="font-size: 9px; color: #64748B;">Chief Neuro-Trainer</span>
                        </div>
                    </td>
                    <td class="signature-box">
                        <div style="height: 35px;"></div>
                        <div class="signature-line">
                            {{ $certificate->authorized_signatory }}<br>
                            <span style="font-size: 9px; color: #64748B;">Founder & Program Director</span>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="cert-meta">
                Official Credential Verification Code: <strong>{{ $certificate->verification_code }}</strong> &bull; Verified by Medhaversum Gurukulam Registry
            </div>
        </div>
    </div>
</body>
</html>
