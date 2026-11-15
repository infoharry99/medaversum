<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Medhaversum Gurukulam</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

    <!-- Progressive Web App (PWA / WebAPK) Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#081B38">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Medhaversum">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/icons/icon-192x192.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --app-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --primary-navy: #081B38;
            --primary-dark: #051329;
            --brand-blue: #1E40AF;
            --brand-blue-hover: #1D3898;
            --brand-accent-gold: #C59B27;
            --active-bg: #EBF3FF;
            --active-text: #1E40AF;
            --bg-canvas: #F8FAFC;
            --sidebar-bg: #FFFFFF;
            --sidebar-border: #E5E7EB;
            --card-border: #E8ECEF;
            --text-dark: #1E293B;
            --text-muted: #64748B;
            --text-light-muted: #94A3B8;
            --sidebar-width: 260px;
        }

        body {
            font-family: var(--app-font);
            background-color: var(--bg-canvas);
            color: var(--text-dark);
            font-size: 0.88rem;
            line-height: 1.45;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* SIDEBAR STYLING MATCHING EXACT REFERENCE UI */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: all 0.25s ease-in-out;
            overflow-y: auto;
        }

        .sidebar-brand-container {
            padding: 16px 18px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #F1F5F9;
        }

        .sidebar-brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .sidebar-brand-logo {
            width: 36px;
            height: 36px;
            object-fit: contain;
            border-radius: 6px;
        }

        .sidebar-brand-title {
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--primary-navy);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            line-height: 1.2;
        }

        .sidebar-brand-sub {
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.3px;
        }

        .sidebar-brand-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-light-muted);
        }

        .sidebar-brand-actions a {
            color: var(--text-muted);
            font-size: 0.95rem;
            padding: 2px 4px;
            border-radius: 4px;
            text-decoration: none;
        }

        .sidebar-brand-actions a:hover {
            background: #F1F5F9;
            color: var(--primary-navy);
        }

        .sidebar-search-box {
            padding: 10px 16px;
        }

        .sidebar-search-input {
            position: relative;
        }

        .sidebar-search-input i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light-muted);
            font-size: 0.85rem;
        }

        .sidebar-search-input input {
            width: 100%;
            padding: 7px 10px 7px 32px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 0.8rem;
            color: var(--text-dark);
            outline: none;
            transition: border-color 0.15s ease;
        }

        .sidebar-search-input input:focus {
            border-color: var(--brand-blue);
            background: #FFFFFF;
        }

        .sidebar-content {
            padding: 8px 12px 20px;
            flex-grow: 1;
        }

        .sidebar-section-header {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94A3B8;
            padding: 14px 10px 6px;
        }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            color: #475569;
            font-size: 0.82rem;
            font-weight: 500;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 2px;
            transition: all 0.15s ease;
        }

        .nav-item-link i {
            font-size: 1.05rem;
            color: #94A3B8;
            width: 20px;
            text-align: center;
            transition: color 0.15s ease;
        }

        .nav-item-link:hover {
            background: #F8FAFC;
            color: var(--primary-navy);
        }

        .nav-item-link:hover i {
            color: var(--brand-blue);
        }

        .nav-item-link.active {
            background: var(--active-bg);
            color: var(--active-text);
            font-weight: 700;
        }

        .nav-item-link.active i {
            color: var(--brand-blue);
        }

        .nav-badge-pill {
            margin-left: auto;
            background: #0B1B3D;
            color: #FFFFFF;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
            letter-spacing: 0.3px;
        }

        /* MAIN WRAPPER */
        #content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.25s ease-in-out;
        }

        /* TOP GLOBAL FILTER & NAVBAR HEADER */
        .top-filter-bar {
            background: #FFFFFF;
            border-bottom: 1px solid var(--sidebar-border);
            padding: 14px 28px 12px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .filter-label {
            font-size: 0.68rem;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 4px;
            display: block;
        }

        .filter-control {
            font-size: 0.8rem;
            height: 36px;
            border-radius: 6px;
            border: 1px solid #D1D5DB;
            padding: 6px 12px;
            color: #1F2937;
            background-color: #FFFFFF;
        }

        .filter-control:focus {
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.08);
        }

        /* ACTION BUTTONS MATCHING REFERENCE UI */
        .btn-filter-apply {
            background: #071739;
            color: #FFFFFF;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 6px;
            padding: 7px 18px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s ease;
        }

        .btn-filter-apply:hover {
            background: #0D275A;
            color: #FFFFFF;
        }

        .btn-filter-reset {
            color: #64748B;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 7px 14px;
            text-decoration: none;
            background: transparent;
            border: none;
        }

        .btn-filter-reset:hover {
            color: var(--primary-navy);
            text-decoration: underline;
        }

        .btn-header-secondary {
            background: #FFFFFF;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 6px;
            padding: 7px 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-header-secondary:hover {
            background: #EFF6FF;
            color: #1E40AF;
            border-color: #93C5FD;
        }

        .btn-header-primary {
            background: #081B38;
            color: #FFFFFF;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 6px;
            padding: 7px 16px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-header-primary:hover {
            background: #0E2A54;
            color: #FFFFFF;
        }

        /* CARD AND TABLE STYLES MATCHING REFERENCE */
        .card-custom {
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-custom-header {
            padding: 16px 20px;
            border-bottom: 1px solid #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #FFFFFF;
        }

        .page-title-box {
            display: flex;
            flex-direction: column;
        }

        .page-main-heading {
            font-size: 1.05rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-sub-heading {
            font-size: 0.76rem;
            color: #6B7280;
            margin-top: 2px;
        }

        /* DATA TABLES */
        .table-custom {
            margin-bottom: 0;
            width: 100%;
        }

        .table-custom th {
            font-size: 0.72rem;
            font-weight: 700;
            color: #4B5563;
            background: #FAFAFA;
            border-bottom: 1px solid #E5E7EB;
            padding: 11px 14px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table-custom th.active-col-header {
            background-color: #F0F5FF;
            color: #1E40AF;
        }

        .table-custom td {
            padding: 12px 14px;
            vertical-align: middle;
            font-size: 0.83rem;
            color: #374151;
            border-bottom: 1px solid #F3F4F6;
        }

        .table-custom tbody tr:hover {
            background-color: #F9FAFB;
        }

        /* INITIALS AVATAR */
        .avatar-initials {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 700;
            color: #FFFFFF;
            flex-shrink: 0;
        }

        .avatar-green { background: #10B981; }
        .avatar-blue { background: #3B82F6; }
        .avatar-purple { background: #8B5CF6; }
        .avatar-teal { background: #14B8A6; }
        .avatar-amber { background: #F59E0B; }

        /* CODE BADGE */
        .badge-code-pill {
            background: #F3F4F6;
            color: #374151;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.76rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #E5E7EB;
        }

        /* SOFT YELLOW/GOLD TAG (MATCHING "Cow (CM)") */
        .badge-category-tag {
            background: #FEF9C3;
            color: #854D0E;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #FDE047;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            line-height: 1.1;
        }

        .badge-sub-chart {
            background: #EFF6FF;
            color: #1E40AF;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 2px 5px;
            border-radius: 3px;
            margin-top: 3px;
            border: 1px solid #DBEAFE;
        }

        /* STATUS BADGE MATCHING REFERENCE */
        .badge-status-pill {
            background: #EBF3FF;
            color: #1E40AF;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .badge-status-pending {
            background: #FEF3C7;
            color: #92400E;
        }

        /* ACTION ICONS IN TABLE */
        .action-icon-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #E0E7FF;
            background: #EEF2FF;
            color: #3B82F6;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .action-icon-btn:hover {
            background: #3B82F6;
            color: #FFFFFF;
            border-color: #3B82F6;
        }

        .action-icon-delete {
            background: #FEF2F2;
            color: #EF4444;
            border-color: #FEE2E2;
        }

        .action-icon-delete:hover {
            background: #EF4444;
            color: #FFFFFF;
            border-color: #EF4444;
        }

        /* SORT ARROWS */
        .sort-arrow {
            font-size: 0.7rem;
            color: #9CA3AF;
            margin-left: 2px;
        }

        /* STAT CARDS */
        .stat-card {
            background: #FFFFFF;
            border-radius: 10px;
            padding: 16px 18px;
            border: 1px solid #E5E7EB;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .stat-card-info h6 {
            font-size: 0.72rem;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .stat-card-info h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-navy);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .stat-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .icon-navy { background: #F1F5F9; color: var(--primary-navy); }
        .icon-royal { background: #EFF6FF; color: var(--brand-blue); }
        .icon-gold { background: #FEF9C3; color: #854D0E; }
        .icon-green { background: #DCFCE7; color: #15803D; }
        .icon-red { background: #FEE2E2; color: #B91C1C; }

        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
            }
            #sidebar.show {
                transform: translateX(0);
            }
            #content-wrapper {
                margin-left: 0;
            }
            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.4);
                z-index: 1035;
                display: none;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- SIDEBAR WITH EXACT MATCHING STRUCTURE & GROUPS -->
    <aside id="sidebar">
        <!-- Brand Header with Logo & Admin Title -->
        <div class="sidebar-brand-container">
            <a href="{{ route('dashboard') }}" class="sidebar-brand-link">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="sidebar-brand-logo">
                <div>
                    <div class="sidebar-brand-title">GURUKULAM ADMIN</div>
                    <div class="sidebar-brand-sub">MEDHAVERSUM</div>
                </div>
            </a>
            <div class="sidebar-brand-actions">
                <a href="{{ route('settings.index') }}" title="Settings"><i class="bi bi-box-arrow-up-right"></i></a>
                <a href="javascript:void(0)" id="sidebarMinimizeBtn" title="Toggle"><i class="bi bi-layout-sidebar"></i></a>
            </div>
        </div>

        <!-- Sidebar In-line Menu Search -->
        <div class="sidebar-search-box">
            <div class="sidebar-search-input">
                <i class="bi bi-search"></i>
                <input type="text" id="sidebarSearch" placeholder="Search menu...">
            </div>
        </div>

        <!-- Grouped Navigation Items -->
        <div class="sidebar-content" id="sidebarNav">
            <!-- SECTION: OVERVIEW -->
            <div class="sidebar-section-header">OVERVIEW</div>
            <a href="{{ route('dashboard') }}" class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>

            <!-- SECTION: STUDENTS & ACADEMICS -->
            <div class="sidebar-section-header">ACADEMICS & STUDENTS</div>
            <a href="{{ route('students.index') }}" class="nav-item-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Students Master</span>
            </a>
            <a href="{{ route('enquiries.index') }}" class="nav-item-link {{ request()->routeIs('enquiries.*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i>
                <span>Enquiries & Admissions</span>
                @php $enqCount = \App\Models\Enquiry::where('status', 'New')->count(); @endphp
                @if($enqCount > 0)
                    <span class="nav-badge-pill">{{ $enqCount }} NEW</span>
                @endif
            </a>
            <a href="{{ route('courses.index') }}" class="nav-item-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Courses Master</span>
            </a>
            <a href="{{ route('batches.index') }}" class="nav-item-link {{ request()->routeIs('batches.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                <span>Batches & Timetable</span>
            </a>

            <!-- SECTION: FINANCE & FEES -->
            <div class="sidebar-section-header">FINANCE & FEES</div>
            <a href="{{ route('fees.index') }}" class="nav-item-link {{ request()->routeIs('fees.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Fees & Payments</span>
            </a>
            <a href="{{ route('attendance.students') }}" class="nav-item-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Attendance Log</span>
            </a>

            <!-- SECTION: COGNITIVE EVALUATION -->
            <div class="sidebar-section-header">EVALUATION & CERTIFICATES</div>
            <a href="{{ route('progress.index') }}" class="nav-item-link {{ request()->routeIs('progress.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>
                <span>Cognitive Progress</span>
            </a>
            <a href="{{ route('certificates.index') }}" class="nav-item-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
                <i class="bi bi-award"></i>
                <span>Certificates</span>
            </a>

            <!-- SECTION: OPERATIONS & ASSETS -->
            <div class="sidebar-section-header">OPERATIONS & ASSETS</div>
            <a href="{{ route('staff.index') }}" class="nav-item-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Staff & Trainers</span>
            </a>
            <a href="{{ route('media.index') }}" class="nav-item-link {{ request()->routeIs('media.*') ? 'active' : '' }}">
                <i class="bi bi-images"></i>
                <span>Media Library</span>
            </a>

            <!-- SECTION: INTELLIGENCE & SYSTEM -->
            <div class="sidebar-section-header">SYSTEM & ANALYTICS</div>
            <a href="{{ route('reports.index') }}" class="nav-item-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Reports & Analytics</span>
            </a>
            <a href="{{ route('notifications.index') }}" class="nav-item-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
                @php $unreadCount = \App\Models\Notification::where('is_read', false)->count(); @endphp
                @if($unreadCount > 0)
                    <span class="nav-badge-pill">{{ $unreadCount }}</span>
                @endif
            </a>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('admin'))
            <a href="{{ route('roles.index') }}" class="nav-item-link {{ request()->routeIs('roles.*') || request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i>
                <span>Roles & Permissions</span>
            </a>
            @endif
            <a href="{{ route('settings.index') }}" class="nav-item-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        </div>

        <!-- Sidebar footer -->
        <div class="p-3 border-top bg-light text-center" style="font-size: 0.72rem; color: #94A3B8;">
            <button type="button" id="btnPwaInstall" class="btn btn-sm btn-header-secondary w-100 mb-2 d-none" style="font-size: 0.74rem;">
                <i class="bi bi-download text-primary me-1"></i> Install App (APK)
            </button>
            Medhaversum Gurukulam &bull; v2.0
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <div id="content-wrapper">
        <!-- GLOBAL TOP NAV HEADER -->
        <div class="d-flex justify-content-between align-items-center bg-white px-4 py-2 border-bottom d-lg-none">
            <button class="btn btn-light btn-sm border" id="sidebarToggleBtn">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div class="fw-bold text-dark">MEDHAVERSUM ADMIN</div>
            <div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger p-0 small text-decoration-none">Exit</button>
                </form>
            </div>
        </div>

        <!-- MAIN VIEW CONTAINER -->
        <main class="flex-grow-1 p-3 p-md-4">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 rounded-3 mb-3" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 rounded-3 mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="p-3 px-4 bg-white border-top text-center text-md-between d-md-flex align-items-center justify-content-between small text-muted" style="font-size: 0.78rem;">
            <div>
                &copy; {{ date('Y') }} <strong>Medhaversum Gurukulam</strong> &bull; A Universe of Infinite Potential. All rights reserved.
            </div>
            <div class="d-flex align-items-center gap-3 mt-2 mt-md-0">
                <a href="{{ route('profile') }}" class="text-secondary text-decoration-none"><i class="bi bi-person me-1"></i> {{ auth()->user()->name }} ({{ auth()->user()->role->name ?? 'User' }})</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none"><i class="bi bi-box-arrow-right me-1"></i> Sign Out</button>
                </form>
            </div>
        </footer>
    </div>

    <!-- PWA Install Banner (Floating on Mobile / Tablet) -->
    <div id="pwaInstallBanner" class="position-fixed bottom-0 start-50 translate-middle-x mb-3 p-3 bg-white border shadow-lg rounded-3 d-none align-items-center gap-3 z-3" style="max-width: 440px; width: 92%; z-index: 9999;">
        <img src="{{ asset('assets/images/icons/icon-96x96.png') }}" alt="App Icon" style="width: 44px; height: 44px; border-radius: 8px;">
        <div class="flex-grow-1">
            <div class="fw-bold text-dark" style="font-size: 0.85rem;">Install Medhaversum App</div>
            <div class="text-muted text-xs" style="font-size: 0.72rem;">Install fast standalone APK on your device</div>
        </div>
        <button id="pwaBannerInstallBtn" class="btn btn-sm btn-header-primary px-3 py-1 text-nowrap">Install</button>
        <button id="pwaBannerCloseBtn" class="btn-close" style="font-size: 0.7rem;"></button>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Register PWA Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('PWA ServiceWorker registered:', reg.scope))
                    .catch((err) => console.log('PWA ServiceWorker error:', err));
            });
        }

        // Handle PWA Install Prompt (Android APK / WebAPK install)
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            $('#btnPwaInstall').removeClass('d-none');
            $('#pwaInstallBanner').removeClass('d-none').addClass('d-flex');
        });

        $(document).on('click', '#btnPwaInstall, #pwaBannerInstallBtn', function() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted PWA installation');
                    }
                    deferredPrompt = null;
                    $('#btnPwaInstall').addClass('d-none');
                    $('#pwaInstallBanner').removeClass('d-flex').addClass('d-none');
                });
            }
        });

        $('#pwaBannerCloseBtn').on('click', function() {
            $('#pwaInstallBanner').removeClass('d-flex').addClass('d-none');
        });

        // Sidebar In-line Menu Filter
        $('#sidebarSearch').on('keyup', function() {
            const val = $(this).val().toLowerCase();
            $('#sidebarNav .nav-item-link').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(val) > -1);
            });
        });

        // Mobile Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('sidebarToggleBtn');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', () => {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            });
        }

        // SweetAlert2 Confirmation
        $(document).on('click', '.btn-delete-confirm', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "This record will be safely removed or archived.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#081B38',
                cancelButtonColor: '#EF4444',
                confirmButtonText: 'Yes, proceed'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
