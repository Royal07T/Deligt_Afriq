<?php

namespace App\Support\Seo;

use App\Models\ContentPage;
use App\Models\Product;
use App\Support\Catalogue\ProductStatus;
use Illuminate\Support\Collection;

final class SitemapGenerator
{
    public function generate(): string
    {
        $urls = collect([
            $this->entry('/', now(), '1.0', 'daily'),
            $this->entry('/collections', now(), '0.9', 'weekly'),
            $this->entry('/gallery', now(), '0.6', 'monthly'),
        ]);

        $urls = $urls
            ->merge($this->collection(Product::query()->where('status', ProductStatus::ACTIVE)->orderBy('id')->get(), 'products/', '0.8', 'weekly'))
            ->merge($this->collection(ContentPage::query()->published()->orderBy('id')->get(), 'pages/', '0.6', 'monthly'));

        return '<?xml version="1.0" encoding="UTF-8"?>'
            ."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .$urls->map(fn (array $url): string => "\n  <url><loc>".e($url['loc']).'</loc><lastmod>'.$url['lastmod'].'</lastmod><changefreq>'.$url['changefreq'].'</changefreq><priority>'.$url['priority'].'</priority></url>')->implode('')
            ."\n".'</urlset>'."\n";
    }

    private function collection(Collection $models, string $prefix, string $priority, string $frequency): Collection
    {
        return $models->map(fn ($model): array => $this->entry('/'.$prefix.$model->slug, $model->updated_at, $priority, $frequency));
    }

    private function entry(string $path, mixed $lastModified, string $priority, string $frequency): array
    {
        return [
            'loc' => url($path),
            'lastmod' => $lastModified?->toAtomString() ?? now()->toAtomString(),
            'priority' => $priority,
            'changefreq' => $frequency,
        ];
    }
}
