<?php

namespace App\Providers;

use Illuminate\Database\Connectors\PostgresConnector;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Neon SNI workaround: inject endpoint into pgsql DSN for older libpq
        if ($endpoint = env('DB_NEON_ENDPOINT')) {
            $this->app->bind('db.connector.pgsql', function () use ($endpoint) {
                return new class($endpoint) extends PostgresConnector {
                    public function __construct(private string $neonEndpoint) {}

                    public function getDsn(array $config): string
                    {
                        $dsn = parent::getDsn($config);
                        // Append Neon endpoint option to DSN
                        return $dsn . ";options='endpoint=" . $this->neonEndpoint . "'";
                    }
                };
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Set auto login (remember me) duration to 1 day (1440 minutes)
        if (\Illuminate\Support\Facades\Auth::guard('web') instanceof \Illuminate\Auth\SessionGuard) {
            \Illuminate\Support\Facades\Auth::guard('web')->setRememberDuration(1440);
        }
    }
}
