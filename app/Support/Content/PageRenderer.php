<?php

namespace App\Support\Content;

final class PageRenderer
{
    public static function paragraphs(?string $body): string
    {
        $paragraphs = preg_split('/\n\s*\n/', trim($body ?? '')) ?: [];

        return collect($paragraphs)
            ->filter()
            ->map(fn (string $paragraph): string => '<p>'.nl2br(e($paragraph)).'</p>')
            ->implode('');
    }
}
