<?php

namespace Tests\Feature\Customer;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_update_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('customer.profile.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'phone' => '+2348031111111',
            'marketing_opt_in' => true,
        ])->assertSessionHas('status');

        $this->assertSame('Updated Name', $user->refresh()->name);
        $this->assertTrue($user->marketing_opt_in);
    }

    public function test_changing_email_requires_reverification(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('customer.profile.update'), [
            'name' => $user->name,
            'email' => 'new-address@example.com',
        ]);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_customer_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create(['password' => 'fashion12345']);

        $this->actingAs($user)->put(route('customer.password.update'), [
            'current_password' => 'fashion12345',
            'password' => 'anotherfashion1',
            'password_confirmation' => 'anotherfashion1',
        ])->assertSessionHas('status');

        $this->assertTrue(password_verify('anotherfashion1', $user->refresh()->password));
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => 'fashion12345']);

        $this->actingAs($user)->put(route('customer.password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'anotherfashion1',
            'password_confirmation' => 'anotherfashion1',
        ])->assertSessionHasErrors('current_password');
    }
}
