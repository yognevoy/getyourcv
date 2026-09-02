<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Models\UserProvider;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use RuntimeException;

class ResolveSocialUser
{
    /**
     * Finds the user linked to this provider account, links it to an existing
     * user with the same verified email, or creates a new user.
     *
     * @throws RuntimeException if the provider did not return a usable email
     */
    public function execute(string $provider, SocialiteUser $socialiteUser): User
    {
        $existing = UserProvider::query()
            ->where('provider', $provider)
            ->where('provider_id', $socialiteUser->getId())
            ->first();

        if ($existing) {
            return $existing->user;
        }

        $email = $socialiteUser->getEmail();

        if (! $email) {
            throw new RuntimeException("No verified email available from {$provider}.");
        }

        return DB::transaction(function () use ($provider, $socialiteUser, $email) {
            $user = User::query()->where('email', $email)->first();

            if (! $user) {
                $user = User::create([
                    'name' => $socialiteUser->getName() ?: $socialiteUser->getNickname() ?: $email,
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => null,
                ]);
            } elseif (! $user->email_verified_at) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            $user->providers()->create([
                'provider' => $provider,
                'provider_id' => $socialiteUser->getId(),
            ]);

            return $user;
        });
    }
}
