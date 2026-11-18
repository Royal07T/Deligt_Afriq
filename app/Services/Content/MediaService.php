<?php

namespace App\Services\Content;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class MediaService
{
    public function store(?UploadedFile $file, string $directory, ?string $currentPath = null): ?string
    {
        if (! $file) {
            return $currentPath;
        }

        $path = $file->store($directory, 'public');

        if ($currentPath) {
            Storage::disk('public')->delete($currentPath);
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
