<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - Medhaversum Gurukulam CRM</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Cinzel:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --mg-navy: #0B1B3D;
            --mg-navy-dark: #071126;
            --mg-royal: #1E40AF;
            --mg-gold: #C59B27;
            --mg-gold-light: #F3E5AB;
            --mg-gold-gradient: linear-gradient(135deg, #D4AF37 0%, #C59B27 50%, #996515 100%);
            --mg-bg: #F8FAFC;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #071126 0%, #0B1B3D 60%, #162a56 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1E293B;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(30, 64, 175, 0.25) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(212, 175, 55, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 10;
        }

        .auth-header {
            text-align: center;
            padding: 35px 30px 20px;
            background: #FAFCFE;
            border-bottom: 1px solid #EEF2F6;
        }

        .auth-logo {
            width: 110px;
            height: 110px;
            object-fit: contain;
            filter: drop-shadow(0 8px 16px rgba(11, 27, 61, 0.15));
            margin-bottom: 12px;
            transition: transform 0.3s ease;
        }

        .auth-logo:hover {
            transform: scale(1.04);
        }

        .auth-title {
            font-family: 'Cinzel', serif;
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--mg-navy);
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .auth-subtitle {
            font-size: 0.85rem;
            color: #64748B;
            font-weight: 500;
        }

        .auth-body {
            padding: 35px 35px 25px;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--mg-navy);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--mg-royal);
            box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
        }

        .btn-mg-gold {
            background: var(--mg-gold-gradient);
            color: #FFFFFF;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(197, 155, 39, 0.35);
        }

        .btn-mg-gold:hover {
            background: linear-gradient(135deg, #e5be48 0%, #d4af37 50%, #aa7219 100%);
            color: #FFFFFF;
            box-shadow: 0 6px 20px rgba(197, 155, 39, 0.5);
            transform: translateY(-1px);
        }

        .auth-footer {
            padding: 20px 30px;
            text-align: center;
            background: #FAFCFE;
            border-top: 1px solid #EEF2F6;
            font-size: 0.82rem;
            color: #94A3B8;
        }

        .demo-credentials {
            background: #F1F5F9;
            border: 1px dashed #CBD5E1;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 0.8rem;
            color: #334155;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
