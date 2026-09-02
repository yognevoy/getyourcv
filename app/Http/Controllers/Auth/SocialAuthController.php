<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ResolveSocialUser;
use App\Http\Controllers\Controller;
use App\Http\Security\SafeRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $provider, Request $request): RedirectResponse
    {
        $request->session()->put('social_auth_redirect', SafeRedirect::path($request->query('redirect')));

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, ResolveSocialUser $action): RedirectResponse
    {
        $redirect = session()->pull('social_auth_redirect');

        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return SafeRedirect::to($redirect, 'login')
                ->with('status', 'That sign in attempt failed. Please try again.');
        }

        try {
            $user = $action->execute($provider, $socialiteUser);
        } catch (RuntimeException) {
            return SafeRedirect::to($redirect, 'login')
                ->with('status', "We couldn't get a verified email from your ".ucfirst($provider).' account.');
        }

        Auth::login($user, remember: true);

        return SafeRedirect::to($redirect, 'dashboard');
    }
}
