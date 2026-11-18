<?php

namespace App\Support\Media;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class MediaUrl
{
    public static function for(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        if (Str::startsWith($path, ['media/', 'storage/'])) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }
}
