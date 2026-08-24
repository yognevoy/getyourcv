<?php

namespace App\Http\Security;

use Illuminate\Http\RedirectResponse;

/**
 * Restricts redirect targets to same-site paths.
 */
final class SafeRedirect
{
    private function __construct()
    {
    }

    /**
     * Returns $path if it's a same-site relative path, null otherwise.
     */
    public static function path(?string $path): ?string
    {
        if ($path === null || $path === '' || ! str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return null;
        }

        return $path;
    }

    public static function to(?string $path, string $fallbackRoute): RedirectResponse
    {
        return redirect(self::path($path) ?? route($fallbackRoute, absolute: false));
    }
}
