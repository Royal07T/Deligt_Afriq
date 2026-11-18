<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\NewsletterSubscribeRequest;
use App\Models\NewsletterSubscriber;
use App\Notifications\NewsletterVerificationNotification;
use App\Notifications\NewsletterWelcomeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(NewsletterSubscribeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'] ?? null,
                'status' => NewsletterSubscriber::PENDING,
                'token' => Str::random(64),
                'subscribed_at' => null,
                'unsubscribed_at' => null,
            ],
        );

        Notification::send($subscriber, new NewsletterVerificationNotification($subscriber));

        return redirect()->back()->with('status', 'Please check your email to confirm your subscription.');
    }

    public function verify(string $token): RedirectResponse
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();

        if ($subscriber->status !== NewsletterSubscriber::SUBSCRIBED) {
            $subscriber->update([
                'status' => NewsletterSubscriber::SUBSCRIBED,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ]);

            Notification::send($subscriber, new NewsletterWelcomeNotification($subscriber));
        }

        return redirect()->route('home')->with('status', 'Your newsletter subscription is confirmed.');
    }

    public function unsubscribe(string $token): RedirectResponse
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();

        if ($subscriber->status !== NewsletterSubscriber::UNSUBSCRIBED) {
            $subscriber->update([
                'status' => NewsletterSubscriber::UNSUBSCRIBED,
                'unsubscribed_at' => now(),
            ]);
        }

        return redirect()->route('home')->with('status', 'You have been unsubscribed from the newsletter.');
    }
}
