<?php

namespace App\Services\Photo;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Manages resume photo files on disk.
 */
class ResumePhotoStore
{
    private const DISK = 'local';

    private const DIRECTORY = 'resume-photos';

    public function store(UploadedFile $file): string
    {
        $filename = Str::uuid().'.'.($file->extension() ?: 'jpg');

        return $file->storeAs(self::DIRECTORY, $filename, self::DISK);
    }

    public function copy(?string $path): ?string
    {
        if (! $this->exists($path)) {
            return null;
        }

        $clonedPath = self::DIRECTORY.'/'.Str::uuid().'.'.pathinfo($path, PATHINFO_EXTENSION);

        Storage::disk(self::DISK)->copy($path, $clonedPath);

        return $clonedPath;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    public function absolutePath(?string $path): ?string
    {
        if (! $this->exists($path)) {
            return null;
        }

        return Storage::disk(self::DISK)->path($path);
    }

    public function response(string $path)
    {
        return Storage::disk(self::DISK)->response($path);
    }

    public function exists(?string $path): bool
    {
        return (bool) $path && Storage::disk(self::DISK)->exists($path);
    }
}
