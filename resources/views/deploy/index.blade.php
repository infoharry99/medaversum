<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Deployment & Artisan Console - Medhaversum Gurukulam</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: #1E293B;
            font-size: 0.88rem;
        }
        .header-bar {
            background: #081B38;
            color: #FFFFFF;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-panel {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .card-panel-header {
            padding: 14px 18px;
            border-bottom: 1px solid #F1F5F9;
            font-weight: 700;
            color: #0F172A;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .btn-action-primary {
            background: #071739;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 0.82rem;
            padding: 9px 16px;
            border-radius: 6px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .btn-action-primary:hover {
            background: #112A61;
            color: #FFFFFF;
        }
        .btn-action-secondary {
            background: #FFFFFF;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
            font-weight: 600;
            font-size: 0.82rem;
            padding: 9px 16px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .btn-action-secondary:hover {
            background: #EFF6FF;
            color: #1E40AF;
        }
        .btn-action-danger {
            background: #FEF2F2;
            color: #DC2626;
            border: 1px solid #FECACA;
            font-weight: 600;
            font-size: 0.82rem;
            padding: 9px 16px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .btn-action-danger:hover {
            background: #DC2626;
            color: #FFFFFF;
        }
        .terminal-box {
            background: #050E21;
            color: #34D399;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.84rem;
            line-height: 1.5;
            padding: 16px;
            border-radius: 8px;
            min-height: 220px;
            max-height: 480px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
            border: 1px solid #1E293B;
        }
        .badge-code {
            font-family: monospace;
            background: #F1F5F9;
            color: #334155;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.78rem;
        }
    </style>
</head>
<body>

    <!-- TOP HEADER -->
    <header class="header-bar">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="width: 38px; height: 38px; border-radius: 6px;">
            <div>
                <h6 class="m-0 fw-bold tracking-wide">MEDHAVERSUM GURUKULAM</h6>
                <span class="text-xs text-white text-opacity-75" style="font-size: 0.72rem;">Live Server Deployment & Artisan Runner Utility</span>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm px-3 rounded-pill" style="font-size: 0.78rem;">
                <i class="bi bi-speedometer2 me-1"></i> CRM Dashboard
            </a>
        </div>
    </header>

    <div class="container py-4">
        <!-- SERVER ENVIRONMENT SUMMARY -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card-panel p-3 mb-0">
                    <span class="text-muted text-xs d-block mb-1" style="font-size: 0.72rem; font-weight: 700;">PHP VERSION</span>
                    <h5 class="fw-bold m-0 text-dark">{{ $envInfo['php_version'] }}</h5>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card-panel p-3 mb-0">
                    <span class="text-muted text-xs d-block mb-1" style="font-size: 0.72rem; font-weight: 700;">LARAVEL VERSION</span>
                    <h5 class="fw-bold m-0 text-dark">v{{ $envInfo['laravel_version'] }}</h5>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card-panel p-3 mb-0">
                    <span class="text-muted text-xs d-block mb-1" style="font-size: 0.72rem; font-weight: 700;">DATABASE</span>
                    <h5 class="fw-bold m-0 text-dark">{{ $envInfo['db_name'] ?? 'Configured' }}</h5>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card-panel p-3 mb-0">
                    <span class="text-muted text-xs d-block mb-1" style="font-size: 0.72rem; font-weight: 700;">STORAGE LINK</span>
                    <h5 class="fw-bold m-0 {{ $envInfo['storage_linked'] ? 'text-success' : 'text-danger' }}">
                        {{ $envInfo['storage_linked'] ? 'Linked' : 'Not Linked' }}
                    </h5>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- COMMAND ACTION BUTTONS -->
            <div class="col-lg-6">
                <!-- PRIMARY MIGRATIONS & SEEDS -->
                <div class="card-panel">
                    <div class="card-panel-header">
                        <span><i class="bi bi-database-fill-gear text-primary me-2"></i> Database & Migrations</span>
                    </div>
                    <div class="p-3 d-flex flex-column gap-2">
                        <!-- Run Migration -->
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                            <div>
                                <div class="fw-bold text-dark">Run Migrations</div>
                                <span class="text-muted text-xs" style="font-size: 0.74rem;">Executes pending database tables (<code class="badge-code">migrate --force</code>)</span>
                            </div>
                            <button type="button" class="btn-action-primary btn-run-command" data-action="migrate">
                                <i class="bi bi-play-fill"></i> Run Migrate
                            </button>
                        </div>

                        <!-- Run Database Seeder -->
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                            <div>
                                <div class="fw-bold text-dark">Run Database Seeder</div>
                                <span class="text-muted text-xs" style="font-size: 0.74rem;">Populates default roles, courses, staff & settings (<code class="badge-code">db:seed --force</code>)</span>
                            </div>
                            <button type="button" class="btn-action-secondary btn-run-command" data-action="seed">
                                <i class="bi bi-seedling"></i> Run Seed
                            </button>
                        </div>

                        <!-- Run Both (Migrate + Seed) -->
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                            <div>
                                <div class="fw-bold text-dark">Full Setup: Migrate & Seed</div>
                                <span class="text-muted text-xs" style="font-size: 0.74rem;">Creates all tables and seeds starter data in one go</span>
                            </div>
                            <button type="button" class="btn-action-primary btn-run-command" data-action="migrate-seed" style="background:#047857;">
                                <i class="bi bi-database-check"></i> Migrate & Seed
                            </button>
                        </div>

                        <!-- Fresh Migrate & Seed (Dangerous) -->
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                            <div>
                                <div class="fw-bold text-danger">Reset & Fresh Seed</div>
                                <span class="text-muted text-xs" style="font-size: 0.74rem;">Wipes all tables and recreates from scratch (<code class="badge-code">migrate:fresh --seed</code>)</span>
                            </div>
                            <button type="button" class="btn-action-danger" id="btnFreshModalTrigger">
                                <i class="bi bi-trash3-fill"></i> Fresh Setup
                            </button>
                        </div>
                    </div>
                </div>

                <!-- SYSTEM OPTIMIZATION & ASSETS -->
                <div class="card-panel">
                    <div class="card-panel-header">
                        <span><i class="bi bi-lightning-charge-fill text-warning me-2"></i> Cache, Storage & Optimization</span>
                    </div>
                    <div class="p-3 d-flex flex-column gap-2">
                        <!-- Storage Link -->
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                            <div>
                                <div class="fw-bold text-dark">Storage Symlink</div>
                                <span class="text-muted text-xs" style="font-size: 0.74rem;">Links <code class="badge-code">storage/app/public</code> to <code class="badge-code">public/storage</code></span>
                            </div>
                            <button type="button" class="btn-action-secondary btn-run-command" data-action="storage-link">
                                <i class="bi bi-link-45deg"></i> Link Storage
                            </button>
                        </div>

                        <!-- Clear Cache -->
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                            <div>
                                <div class="fw-bold text-dark">Clear All Caches</div>
                                <span class="text-muted text-xs" style="font-size: 0.74rem;">Flushes config, route, view, and app cache (<code class="badge-code">optimize:clear</code>)</span>
                            </div>
                            <button type="button" class="btn-action-secondary btn-run-command" data-action="clear-cache">
                                <i class="bi bi-arrow-clockwise"></i> Clear Cache
                            </button>
                        </div>

                        <!-- Production Optimize -->
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                            <div>
                                <div class="fw-bold text-dark">Production Optimize</div>
                                <span class="text-muted text-xs" style="font-size: 0.74rem;">Caches routes and configurations for maximum speed</span>
                            </div>
                            <button type="button" class="btn-action-primary btn-run-command" data-action="optimize">
                                <i class="bi bi-rocket-takeoff-fill"></i> Optimize
                            </button>
                        </div>

                        <!-- Generate App Key -->
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                            <div>
                                <div class="fw-bold text-dark">Generate App Key</div>
                                <span class="text-muted text-xs" style="font-size: 0.74rem;">Generates a new AES-256 encryption key</span>
                            </div>
                            <button type="button" class="btn-action-secondary btn-run-command" data-action="key-generate">
                                <i class="bi bi-key-fill"></i> Key Generate
                            </button>
                        </div>
                    </div>
                </div>

                <!-- DIRECT WEB ENDPOINTS INFO -->
                <div class="card-panel">
                    <div class="card-panel-header">
                        <span><i class="bi bi-globe me-2"></i> Direct Browser / cURL URLs</span>
                    </div>
                    <div class="p-3">
                        <p class="text-muted text-xs mb-2" style="font-size: 0.76rem;">
                            You can also trigger commands directly via browser address bar or cron jobs without logging in:
                        </p>
                        <ul class="list-unstyled d-flex flex-column gap-2 m-0" style="font-size: 0.76rem;">
                            <li><strong class="text-dark">Migrate:</strong> <a href="{{ route('deploy.run', ['action' => 'migrate', 'key' => $validKey]) }}" target="_blank" class="text-primary text-break">{{ route('deploy.run', ['action' => 'migrate', 'key' => $validKey]) }}</a></li>
                            <li><strong class="text-dark">Seed:</strong> <a href="{{ route('deploy.run', ['action' => 'seed', 'key' => $validKey]) }}" target="_blank" class="text-primary text-break">{{ route('deploy.run', ['action' => 'seed', 'key' => $validKey]) }}</a></li>
                            <li><strong class="text-dark">Storage Link:</strong> <a href="{{ route('deploy.run', ['action' => 'storage-link', 'key' => $validKey]) }}" target="_blank" class="text-primary text-break">{{ route('deploy.run', ['action' => 'storage-link', 'key' => $validKey]) }}</a></li>
                            <li><strong class="text-dark">Clear Cache:</strong> <a href="{{ route('deploy.run', ['action' => 'clear-cache', 'key' => $validKey]) }}" target="_blank" class="text-primary text-break">{{ route('deploy.run', ['action' => 'clear-cache', 'key' => $validKey]) }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TERMINAL LOG OUTPUT CONSOLE -->
            <div class="col-lg-6">
                <div class="card-panel sticky-top" style="top: 20px;">
                    <div class="card-panel-header">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-terminal-fill text-success"></i> Real-time Execution Console
                        </span>
                        <button type="button" class="btn btn-sm btn-light border py-0 px-2" id="btnClearLog" style="font-size: 0.74rem;">Clear Console</button>
                    </div>
                    <div class="p-3">
                        <div class="terminal-box" id="terminalOutput">Ready. Click any action button on the left to execute commands on the live server...</div>

                        <!-- Custom Command Runner -->
                        <div class="mt-3">
                            <label class="form-label text-xs fw-bold text-muted mb-1" style="font-size: 0.74rem;">RUN ALLOWED ARTISAN COMMAND</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white font-monospace text-muted" style="font-size: 0.8rem;">php artisan</span>
                                <input type="text" id="customCommandInput" class="form-control" placeholder="e.g. migrate:status, route:list, about" style="font-size: 0.82rem;">
                                <button class="btn btn-action-primary" type="button" id="btnRunCustom">Execute</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CONFIRMATION FOR FRESH MIGRATION -->
    <div class="modal fade" id="modalConfirmFresh" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> DANGER: Wipe Database & Re-seed?</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger fw-bold mb-2">Warning: This action will DROP all existing tables and re-run seeders.</p>
                    <p class="text-muted small">Any existing student admissions, fee payments, and attendance will be replaced with fresh seeder records. Type <strong>CONFIRM</strong> below to proceed:</p>
                    <input type="text" id="wipeConfirmationInput" class="form-control" placeholder="Type CONFIRM here">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnExecuteFresh">Execute migrate:fresh --seed</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const securityKey = "{{ $validKey }}";

        function logToTerminal(title, output, isSuccess = true) {
            const time = new Date().toLocaleTimeString();
            const color = isSuccess ? '#34D399' : '#EF4444';
            const logEntry = `\n[${time}] > ${title}\n` +
                             `------------------------------------------------------------\n` +
                             `${output}\n` +
                             `------------------------------------------------------------\n`;
            
            const term = $('#terminalOutput');
            term.append(logEntry);
            term.scrollTop(term[0].scrollHeight);
        }

        // Run Preset Commands via AJAX
        $('.btn-run-command').on('click', function() {
            const btn = $(this);
            const action = btn.data('action');
            const originalHtml = btn.html();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Running...');
            $('#terminalOutput').append(`\n>> Executing [${action}] on server...\n`);

            $.ajax({
                url: "{{ route('deploy.execute') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    key: securityKey,
                    action: action
                },
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html(originalHtml);
                    logToTerminal(res.command, res.output, res.success);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalHtml);
                    let err = xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText;
                    logToTerminal(action + ' (FAILED)', err, false);
                }
            });
        });

        // Run Custom Command
        $('#btnRunCustom').on('click', function() {
            const cmd = $('#customCommandInput').val().trim();
            if (!cmd) return;

            const btn = $(this);
            btn.prop('disabled', true).html('...');
            $('#terminalOutput').append(`\n>> Running custom command: php artisan ${cmd}...\n`);

            $.ajax({
                url: "{{ route('deploy.execute') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    key: securityKey,
                    action: 'custom',
                    custom_command: cmd
                },
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html('Execute');
                    logToTerminal(res.command, res.output, res.success);
                    $('#customCommandInput').val('');
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('Execute');
                    let err = xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText;
                    logToTerminal(cmd + ' (FAILED)', err, false);
                }
            });
        });

        // Trigger Danger Modal
        $('#btnFreshModalTrigger').on('click', function() {
            $('#wipeConfirmationInput').val('');
            const modal = new bootstrap.Modal(document.getElementById('modalConfirmFresh'));
            modal.show();
        });

        // Execute Fresh Migration
        $('#btnExecuteFresh').on('click', function() {
            const confirmVal = $('#wipeConfirmationInput').val().trim();
            if (confirmVal !== 'CONFIRM') {
                alert('Please type CONFIRM exactly to proceed.');
                return;
            }

            const modalEl = document.getElementById('modalConfirmFresh');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            $('#terminalOutput').append(`\n>> DANGER: Executing migrate:fresh --seed on live server...\n`);

            $.ajax({
                url: "{{ route('deploy.execute') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    key: securityKey,
                    action: 'migrate-fresh-seed',
                    confirm: 'yes'
                },
                dataType: "json",
                success: function(res) {
                    logToTerminal(res.command, res.output, res.success);
                },
                error: function(xhr) {
                    let err = xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText;
                    logToTerminal('migrate:fresh (FAILED)', err, false);
                }
            });
        });

        // Clear Terminal Console
        $('#btnClearLog').on('click', function() {
            $('#terminalOutput').text('Console cleared.\n');
        });
    </script>
</body>
</html>
