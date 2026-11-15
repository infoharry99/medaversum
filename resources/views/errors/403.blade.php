<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden | Medhaversum Gurukulam</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: #1E293B;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .error-code {
            font-size: 4rem;
            font-weight: 800;
            color: #071739;
            line-height: 1;
            margin-bottom: 12px;
        }
        .btn-navy {
            background: #071739;
            color: #FFFFFF;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-navy:hover {
            background: #0F2D6B;
            color: #FFFFFF;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">403</div>
        <h4 class="fw-bold mb-3">Unauthorized Access</h4>
        <p class="text-muted small mb-4">
            {{ $message ?? 'You do not have permission to access this protected deployment utility. Please provide your authorized secret key via ?key=YOUR_KEY.' }}
        </p>
        <a href="{{ route('login') }}" class="btn-navy">Return to Login</a>
    </div>
</body>
</html>
