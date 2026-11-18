<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Migrations\Migrator;
use Throwable;

class CheckProductionCommand extends Command
{
    protected $signature = 'app:check-production';

    protected $description = 'Validate deployment configuration for a production release.';

    public function handle(Migrator $migrator, Filesystem $files): int
    {
        $errors = [];
        $warnings = [];

        $checks = [
            'APP_ENV is production' => [config('app.env') === 'production', 'Set APP_ENV=production.'],
            'APP_DEBUG is disabled' => [config('app.debug') === false, 'Set APP_DEBUG=false.'],
            'APP_KEY is configured' => [$this->hasValidAppKey(), 'Run php artisan key:generate.'],
            'APP_URL uses HTTPS' => [str_starts_with((string) config('app.url'), 'https://'), 'Set APP_URL to your HTTPS domain.'],
            'HTTPS is forced' => [(bool) config('app.force_https'), 'Set FORCE_HTTPS=true.'],
            'Session cookies are secure' => [(bool) config('session.secure'), 'Set SESSION_SECURE_COOKIE=true.'],
            'Storage link exists' => [is_link(public_path('storage')), 'Run php artisan storage:link.'],
            'Storage directory is writable' => [is_writable(storage_path()), 'Make the storage directory writable.'],
            'Bootstrap cache is writable' => [is_writable(base_path('bootstrap/cache')), 'Make bootstrap/cache writable.'],
            'Static robots.txt removed' => [! $files->exists(public_path('robots.txt')), 'Remove public/robots.txt so the dynamic route can respond.'],
        ];

        foreach ($checks as $label => [$passed, $message]) {
            if ($passed) {
                $this->info('[OK] '.$label);
            } else {
                $errors[] = $message;
                $this->error('[FAIL] '.$label);
            }
        }

        if (config('app.force_https') && blank(config('deployment.trusted_proxies'))) {
            $warnings[] = 'Configure TRUSTED_PROXIES when HTTPS is terminated by a load balancer.';
            $this->warn('[WARN] Trusted proxies are not configured');
        }

        if (config('store.payment_methods.paystack')) {
            foreach ([
                'services.paystack.public_key' => 'PAYSTACK_PUBLIC_KEY',
                'services.paystack.secret_key' => 'PAYSTACK_SECRET_KEY',
                'services.paystack.webhook_secret' => 'PAYSTACK_WEBHOOK_SECRET',
            ] as $configKey => $envName) {
                if (blank(config($configKey))) {
                    $errors[] = 'Set '.$envName.' when Paystack payments are enabled.';
                    $this->error('[FAIL] '.$envName.' is missing');
                }
            }
        }

        try {
            $pending = $migrator->pendingMigrations();

            if ($pending === []) {
                $this->info('[OK] No pending migrations');
            } else {
                $errors[] = 'Run php artisan migrate --force before deploying.';
                $this->error('[FAIL] '.count($pending).' pending migration(s)');
            }
        } catch (Throwable) {
            $warnings[] = 'Could not verify migration status; check the database connection.';
            $this->warn('[WARN] Migration status unavailable');
        }

        foreach ($warnings as $warning) {
            $this->line('<fg=yellow>Warning:</> '.$warning);
        }

        foreach ($errors as $error) {
            $this->line('<fg=red>Error:</> '.$error);
        }

        if ($errors !== []) {
            $this->error('Production readiness check failed with '.count($errors).' error(s).');

            return self::FAILURE;
        }

        $this->info('Production readiness check passed.');

        return self::SUCCESS;
    }

    private function hasValidAppKey(): bool
    {
        $key = (string) config('app.key');

        if ($key === '' || ! str_starts_with($key, 'base64:')) {
            return false;
        }

        return strlen((string) base64_decode(substr($key, 7), true)) >= 32;
    }
}
