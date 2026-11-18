<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Support\Newsletter\CsvSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', NewsletterSubscriber::class);

        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();

        $query = NewsletterSubscriber::query();

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('email', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%');
            });
        }

        if (in_array($status, [
            NewsletterSubscriber::PENDING,
            NewsletterSubscriber::SUBSCRIBED,
            NewsletterSubscriber::UNSUBSCRIBED,
        ], true)) {
            $query->where('status', $status);
        }

        return view('admin.newsletter.index', [
            'subscribers' => $query->latest()->paginate(20)->withQueryString(),
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function export(Request $request): Response
    {
        $this->authorize('viewAny', NewsletterSubscriber::class);

        $subscribers = NewsletterSubscriber::query()
            ->when($request->string('status')->toString(), function ($query, string $status): void {
                $query->where('status', $status);
            })
            ->orderBy('email')
            ->get();

        $handle = fopen('php://temp', 'w+');

        fputcsv($handle, ['Name', 'Email', 'Status', 'Subscribed at']);

        foreach ($subscribers as $subscriber) {
            fputcsv($handle, [
                CsvSanitizer::cell($subscriber->name),
                CsvSanitizer::cell($subscriber->email),
                CsvSanitizer::cell($subscriber->status),
                CsvSanitizer::cell($subscriber->subscribed_at?->toDateTimeString()),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="newsletter-subscribers-'.now()->format('Y-m-d').'.csv"');
    }

    public function destroy(NewsletterSubscriber $newsletterSubscriber): RedirectResponse
    {
        $this->authorize('delete', $newsletterSubscriber);

        $newsletterSubscriber->delete();

        return redirect()->route('admin.newsletter.index')->with('status', 'Subscriber removed.');
    }
}
