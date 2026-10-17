<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Ensure PHP's default timezone matches the app timezone so
        // Carbon and native PHP date/time functions use Pakistan time.
        if (config('app.timezone')) {
            date_default_timezone_set(config('app.timezone'));
        }

        // Auto-heal stale Vite hot file: If public/hot exists from an inactive "npm run dev",
        // verify the server is listening. If dead, remove the hot file so Laravel automatically
        // falls back to compiled assets without CSS/JS breakage.
        if (file_exists(public_path('hot'))) {
            $hotUrl = trim(@file_get_contents(public_path('hot')));
            if ($hotUrl) {
                $parts = parse_url($hotUrl);
                $host = trim($parts['host'] ?? '127.0.0.1', '[]');
                $port = (int) ($parts['port'] ?? 5173);
                $socket = @fsockopen($host, $port, $errno, $errstr, 0.05);
                if (is_resource($socket)) {
                    fclose($socket);
                } else {
                    @unlink(public_path('hot'));
                }
            }
        }
    }
}
