<?php

namespace App\Http\Security;

use Illuminate\Http\Request;

/**
 * HMAC-keyed fingerprint of a visitor's IP and user agent.
 */
final class ViewerHash
{
    private function __construct() {}

    public static function fromRequest(Request $request): string
    {
        return hash_hmac('sha256', $request->ip().'|'.$request->userAgent(), config('app.key'));
    }
}
