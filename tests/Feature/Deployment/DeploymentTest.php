<?php

namespace Tests\Feature\Deployment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeploymentTest extends TestCase
{
    use RefreshDatabase;

    private bool $createdStorageLink = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (! is_link(public_path('storage'))) {
            @symlink(storage_path('app/public'), public_path('storage'));
            $this->createdStorageLink = true;
        }

        config()->set([
            'app.env' => 'production',
            'app.debug' => false,
            'app.url' => 'https://delightafriq.example',
            'app.force_https' => true,
            'session.secure' => true,
            'deployment.trusted_proxies' => '127.0.0.1',
            'store.payment_methods.paystack' => false,
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->createdStorageLink) {
            @unlink(public_path('storage'));
        }

        parent::tearDown();
    }

    public function test_production_check_passes_with_secure_configuration(): void
    {
        $this->artisan('app:check-production')
            ->expectsOutputToContain('Production readiness check passed.')
            ->assertExitCode(0);
    }

    public function test_production_check_fails_when_debug_is_enabled(): void
    {
        config()->set('app.debug', true);

        $this->artisan('app:check-production')
            ->expectsOutputToContain('Set APP_DEBUG=false.')
            ->assertExitCode(1);
    }
}
