<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_request_page_is_accessible(): void
    {
        $this->get(route('password.request'))->assertOk()->assertSee('Forgot your password?');
    }

    public function test_reset_link_is_sent_to_existing_user(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newfashion123',
            'password_confirmation' => 'newfashion123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(password_verify('newfashion123', $user->refresh()->password));
    }

    public function test_invalid_reset_token_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'newfashion123',
            'password_confirmation' => 'newfashion123',
        ])->assertSessionHas('error');

        $this->assertFalse(password_verify('newfashion123', $user->refresh()->password));
    }

    public function test_unknown_email_does_not_reveal_account_existence(): void
    {
        $this->post(route('password.email'), ['email' => 'nobody@example.com'])
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('users', ['email' => 'nobody@example.com']);
    }
}
