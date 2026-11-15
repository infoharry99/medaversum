<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ArtisanRunnerController extends Controller
{
    /**
     * Get or validate the deployment secret key.
     */
    protected function isAuthorized(Request $request): bool
    {
        // 1. Secret key via URL query or header (prioritized, works with 0 database tables)
        $validKey = config('app.artisan_key', env('ARTISAN_KEY', 'medaversum_live_2026'));
        $providedKey = $request->get('key') ?? $request->header('X-Artisan-Key');

        if (!empty($providedKey) && hash_equals($validKey, (string) $providedKey)) {
            return true;
        }

        // 2. If user is logged in as Super Admin or Admin (safely guarded)
        try {
            if ($request->hasSession() && auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasRole('admin'))) {
                return true;
            }
        } catch (\Throwable $e) {
            // Session or database table does not exist yet (e.g. initial setup)
        }

        return false;
    }

    /**
     * Main Web Console / Dashboard for Live Artisan execution.
     */
    public function index(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return response()->view('errors.403', [
                'message' => 'Unauthorized Access. Please provide a valid ?key=YOUR_SECRET_KEY parameter.'
            ], 403);
        }

        $validKey = $request->get('key', env('ARTISAN_KEY', 'medaversum_live_2026'));
        
        // System Health & Environment info
        $envInfo = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug') ? 'Enabled' : 'Disabled',
            'db_connection' => config('database.default'),
            'db_name' => config('database.connections.' . config('database.default') . '.database'),
            'storage_linked' => public_path('storage') && File::exists(public_path('storage')),
        ];

        return view('deploy.index', compact('envInfo', 'validKey'));
    }

    /**
     * Run a specific Artisan command and return output.
     */
    public function execute(Request $request, $action = null)
    {
        if (!$this->isAuthorized($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid security key.'
            ], 403);
        }

        $action = $action ?? $request->input('action');
        $validKey = $request->get('key', env('ARTISAN_KEY', 'medaversum_live_2026'));

        $output = '';
        $success = true;
        $commandRun = '';

        try {
            switch ($action) {
                case 'migrate':
                    $commandRun = 'migrate --force';
                    Artisan::call('migrate', ['--force' => true]);
                    $output = Artisan::output();
                    break;

                case 'seed':
                    $commandRun = 'db:seed --force';
                    Artisan::call('db:seed', ['--force' => true]);
                    $output = Artisan::output();
                    break;

                case 'migrate-seed':
                    $commandRun = 'migrate --force && db:seed --force';
                    Artisan::call('migrate', ['--force' => true]);
                    $output .= Artisan::output() . "\n";
                    Artisan::call('db:seed', ['--force' => true]);
                    $output .= Artisan::output();
                    break;

                case 'migrate-fresh-seed':
                    if ($request->input('confirm') !== 'yes' && $request->get('confirm') !== 'yes') {
                        return response()->json([
                            'success' => false,
                            'message' => 'SAFETY LOCK: Passing ?confirm=yes is required to execute migrate:fresh on live server.'
                        ], 400);
                    }
                    $commandRun = 'migrate:fresh --seed --force';
                    Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
                    $output = Artisan::output();
                    break;

                case 'storage-link':
                    $commandRun = 'storage:link';
                    // Check if already linked
                    $target = storage_path('app/public');
                    $link = public_path('storage');
                    
                    if (file_exists($link)) {
                        $output = "The [$link] link already exists.";
                    } else {
                        try {
                            Artisan::call('storage:link');
                            $output = Artisan::output();
                        } catch (\Throwable $e) {
                            // Fallback for Windows/cPanel without symlink permissions
                            if (!file_exists($link)) {
                                @symlink($target, $link);
                            }
                            $output = file_exists($link) 
                                ? "Symbolic link created successfully via fallback." 
                                : "Symlink error: " . $e->getMessage();
                        }
                    }
                    break;

                case 'clear-cache':
                    $commandRun = 'optimize:clear';
                    Artisan::call('optimize:clear');
                    $output = Artisan::output();
                    break;

                case 'optimize':
                    $commandRun = 'optimize (config:cache, route:cache, view:cache)';
                    Artisan::call('optimize');
                    $output = Artisan::output();
                    break;

                case 'key-generate':
                    $commandRun = 'key:generate --force';
                    Artisan::call('key:generate', ['--force' => true]);
                    $output = Artisan::output();
                    break;

                case 'custom':
                    $customCmd = trim((string) $request->input('custom_command'));
                    
                    // Disallow hazardous shell escapes
                    $allowedPrefixes = ['migrate', 'db:seed', 'cache:clear', 'config:', 'route:', 'view:', 'storage:link', 'about', 'env'];
                    $isAllowed = false;
                    foreach ($allowedPrefixes as $prefix) {
                        if (str_starts_with($customCmd, $prefix)) {
                            $isAllowed = true;
                            break;
                        }
                    }

                    if (!$isAllowed) {
                        return response()->json([
                            'success' => false,
                            'message' => "Command not in allowed list for live web runner."
                        ], 400);
                    }

                    $commandRun = $customCmd;
                    Artisan::call($customCmd);
                    $output = Artisan::output();
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => "Unknown action [{$action}]. Available: migrate, seed, migrate-seed, migrate-fresh-seed, storage-link, clear-cache, optimize, key-generate"
                    ], 400);
            }

            if (empty(trim($output))) {
                $output = "Command executed successfully with no direct text output.";
            }

        } catch (\Throwable $e) {
            $success = false;
            $output = "ERROR EXECUTING COMMAND:\n" . $e->getMessage() . "\n\nFile: " . $e->getFile() . ':' . $e->getLine();
        }

        // If requested as JSON (via AJAX / CLI / cURL)
        if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => $success,
                'action' => $action,
                'command' => $commandRun,
                'output' => $output,
                'timestamp' => now()->toDateTimeString()
            ]);
        }

        // Return plain response with terminal styling
        return response(
            "<pre style='background:#071739; color:#A7F3D0; padding:20px; font-family:monospace; border-radius:8px; font-size:14px; line-height:1.5; white-space:pre-wrap;'>" .
            "===========================================================\n" .
            " MEDHAVERSUM GURUKULAM - LIVE ARTISAN COMMAND EXECUTION\n" .
            " Command: php artisan {$commandRun}\n" .
            " Time:    " . now()->toDateTimeString() . "\n" .
            " Status:  " . ($success ? "SUCCESS" : "FAILED") . "\n" .
            "===========================================================\n\n" .
            htmlspecialchars($output) .
            "\n\n<a href='" . route('deploy.index', ['key' => $validKey]) . "' style='color:#FDE047; text-decoration:underline;'>&larr; Back to Live Deployment Panel</a>" .
            "</pre>"
        );
    }
}
