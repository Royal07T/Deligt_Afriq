<?php

namespace Tests\Feature\Newsletter;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Notifications\NewsletterVerificationNotification;
use App\Notifications\NewsletterWelcomeNotification;
use App\Support\Access\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccessControl();
    }

    public function test_visitor_can_subscribe_and_receive_verification(): void
    {
        Notification::fake();

        $this->from(route('home'))
            ->post(route('newsletter.subscribe'), [
                'email' => 'Customer@Example.com',
                'name' => 'Customer',
            ])
            ->assertRedirect(route('home'));

        $subscriber = NewsletterSubscriber::where('email', 'customer@example.com')->firstOrFail();
        $this->assertSame(NewsletterSubscriber::PENDING, $subscriber->status);
        $this->assertNotNull($subscriber->token);

        Notification::assertSentTo($subscriber, NewsletterVerificationNotification::class);
    }

    public function test_signed_verification_confirms_subscription_and_sends_welcome(): void
    {
        Notification::fake();

        $subscriber = NewsletterSubscriber::create([
            'email' => 'verified@example.com',
            'name' => 'Verified',
            'status' => NewsletterSubscriber::PENDING,
            'token' => 'verification-token',
        ]);

        $url = URL::temporarySignedRoute(
            'newsletter.verify',
            now()->addDay(),
            ['token' => $subscriber->token],
        );

        $this->get($url)
            ->assertRedirect(route('home'))
            ->assertSessionHas('status', 'Your newsletter subscription is confirmed.');

        $this->assertSame(NewsletterSubscriber::SUBSCRIBED, $subscriber->fresh()->status);
        $this->assertNotNull($subscriber->fresh()->subscribed_at);
        Notification::assertSentTo($subscriber, NewsletterWelcomeNotification::class);
    }

    public function test_verification_requires_a_valid_signature(): void
    {
        NewsletterSubscriber::create([
            'email' => 'unsigned@example.com',
            'status' => NewsletterSubscriber::PENDING,
            'token' => 'unsigned-token',
        ]);

        $this->get(route('newsletter.verify', ['token' => 'unsigned-token']))
            ->assertForbidden();
    }

    public function test_signed_unsubscribe_marks_subscriber_as_unsubscribed(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'leave@example.com',
            'status' => NewsletterSubscriber::SUBSCRIBED,
            'token' => 'unsubscribe-token',
            'subscribed_at' => now(),
        ]);

        $url = URL::temporarySignedRoute(
            'newsletter.unsubscribe',
            now()->addDay(),
            ['token' => $subscriber->token],
        );

        $this->get($url)->assertRedirect(route('home'));

        $this->assertSame(NewsletterSubscriber::UNSUBSCRIBED, $subscriber->fresh()->status);
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
    }

    public function test_admin_export_sanitizes_spreadsheet_formulas(): void
    {
        NewsletterSubscriber::create([
            'email' => 'formula@example.com',
            'name' => '=SUM(1,1)',
            'status' => NewsletterSubscriber::SUBSCRIBED,
            'token' => 'formula-token',
            'subscribed_at' => now(),
        ]);

        $admin = User::factory()->create();
        $admin->assignRole(Roles::SUPER_ADMIN);

        $this->actingAs($admin)
            ->get(route('admin.newsletter.export'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8')
            ->assertSee('\'=SUM(1,1)', false);
    }
}
