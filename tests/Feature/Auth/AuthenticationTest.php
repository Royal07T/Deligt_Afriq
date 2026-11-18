<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\Access\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccessControl();
    }

    public function test_login_page_is_accessible(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Sign in to your account');
    }

    public function test_password_fields_include_visibility_toggle(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Toggle password visibility')
            ->assertSee('x-bind:type="visible ? \'text\' : \'password\'"', false);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Toggle password visibility');

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Toggle password visibility');

        $this->get(route('password.reset', ['token' => 'reset-token']))
            ->assertOk()
            ->assertSee('Toggle password visibility');

        $this->actingAs(User::factory()->create())
            ->get(route('customer.profile.edit'))
            ->assertOk()
            ->assertSee('Toggle password visibility');
    }

    public function test_customer_can_log_in_and_view_dashboard(): void
    {
        $user = User::factory()->create(['password' => 'fashion12345']);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'fashion12345',
        ])->assertRedirect(route('customer.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('customer.dashboard'))->assertOk();
        $this->assertNotNull($user->refresh()->last_login_at);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = User::factory()->create(['password' => 'fashion12345']);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_customer_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_unverified_user_cannot_access_customer_portal(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('customer.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_suspended_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'password' => 'fashion12345',
            'is_active' => false,
            'suspended_at' => now(),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'fashion12345',
        ])->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_super_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Roles::SUPER_ADMIN);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }
}
