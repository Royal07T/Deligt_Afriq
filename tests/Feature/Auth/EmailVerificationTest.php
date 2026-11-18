<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_notice_requires_authentication(): void
    {
        $this->get(route('verification.notice'))->assertRedirect(route('login'));
    }

    public function test_user_can_verify_email_with_valid_signed_link(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect(route('customer.dashboard'));

        $this->assertTrue($user->refresh()->hasVerifiedEmail());
        $this->assertDatabaseHas('activity_logs', ['action' => 'customer.email_verified', 'user_id' => $user->id]);
    }

    public function test_verification_link_with_wrong_hash_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1('another-email@example.com'),
        ]);

        $this->actingAs($user)->get($url)->assertForbidden();
        $this->assertFalse($user->refresh()->hasVerifiedEmail());
    }

    public function test_verification_email_can_be_resent(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertSessionHas('status');

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }
}
