<?php

use App\Models\User;
use App\Models\UserProvider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('redirecting to google stores the safe redirect target in the session', function () {
    $response = $this->get('/auth/google/redirect?redirect=/resumes/new');

    $response->assertSessionHas('social_auth_redirect', '/resumes/new');
});

test('redirecting to google drops an off-site redirect target', function () {
    $response = $this->get('/auth/google/redirect?redirect=https://evil.example.com');

    $response->assertSessionMissing('social_auth_redirect');
});

test('signing in via google creates a new user', function () {
    Socialite::fake('google', SocialiteUser::fake([
        'id' => 'google-1',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]));

    $response = $this->get('/auth/google/callback');

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'jane@example.com')->firstOrFail();
    expect($user->name)->toBe('Jane Doe');
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->password)->toBeNull();

    expect(UserProvider::query()
        ->where('user_id', $user->id)
        ->where('provider', 'google')
        ->where('provider_id', 'google-1')
        ->exists())->toBeTrue();
});

test('signing in via google links to an existing account with the same verified email', function () {
    $user = User::factory()->create(['email' => 'jane@example.com']);

    Socialite::fake('google', SocialiteUser::fake([
        'id' => 'google-1',
        'email' => 'jane@example.com',
    ]));

    $this->get('/auth/google/callback');

    $this->assertAuthenticatedAs($user);
    expect(User::count())->toBe(1);
});

test('signing in via google a second time reuses the linked account', function () {
    Socialite::fake('google', SocialiteUser::fake([
        'id' => 'google-1',
        'email' => 'jane@example.com',
    ]));

    $this->get('/auth/google/callback');
    $this->post('/logout');

    $this->get('/auth/google/callback');

    $this->assertAuthenticated();
    expect(User::count())->toBe(1);
});

test('signing in via google honors the stashed redirect target', function () {
    Socialite::fake('google', SocialiteUser::fake());

    $this->get('/auth/google/redirect?redirect=/resumes/new');
    $response = $this->get('/auth/google/callback');

    $response->assertRedirect('/resumes/new');
});

test('signing in via google without a usable email fails gracefully', function () {
    Socialite::fake('google', SocialiteUser::fake(['email' => null]));

    $response = $this->get('/auth/google/callback');

    $this->assertGuest();
    $response->assertRedirect(route('login', absolute: false));
});
