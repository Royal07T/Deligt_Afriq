<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\RegistrationConfirmation;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_is_accessible(): void
    {
        $this->get(route('register'))->assertOk()->assertSee('Join Delight Afriq');
    }

    public function test_new_customer_can_register(): void
    {
        Notification::fake();

        $response = $this->post(route('register'), [
            'name' => 'Ada Okafor',
            'email' => 'ada@example.com',
            'phone' => '+2348030000000',
            'password' => 'fashion12345',
            'password_confirmation' => 'fashion12345',
        ]);

        $user = User::where('email', 'ada@example.com')->firstOrFail();

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', ['email' => 'ada@example.com', 'is_active' => 1]);
        Notification::assertSentTo($user, VerifyEmailNotification::class);
        Notification::assertSentTo($user, RegistrationConfirmation::class);
        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertFalse($user->isStaff());
    }

    public function test_registration_rejects_invalid_input(): void
    {
        $this->post(route('register'), [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ])->assertSessionHasErrors(['name', 'email', 'password']);

        $this->assertGuest();
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post(route('register'), [
            'name' => 'Another User',
            'email' => 'taken@example.com',
            'password' => 'fashion12345',
            'password_confirmation' => 'fashion12345',
        ])->assertSessionHasErrors('email');
    }
}
