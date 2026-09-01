<?php

use App\Actions\Resume\CreateResume;
use App\Models\User;

test('a guest can view a resume by its public slug', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'published',
    ]);

    $response = $this->get("/r/{$resume->slug}");

    $response->assertInertia(fn ($page) => $page
        ->component('Public/Resume')
        ->where('available', true)
        ->where('resume.full_name', 'Jane Doe')
    );
});

test('an unknown slug returns a 404', function () {
    $response = $this->get('/r/does-not-exist');

    $response->assertNotFound();
});

test('a draft resume shows as unavailable instead of 404', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);

    $response = $this->get("/r/{$resume->slug}");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Public/Resume')
        ->where('available', false)
        ->missing('resume')
    );
});

test('an archived resume shows as unavailable', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'published',
    ]);
    $resume->update(['archived_at' => now()]);

    $response = $this->get("/r/{$resume->slug}");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->where('available', false));
});

test('a trashed resume shows as unavailable instead of 404', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'published',
    ]);
    $resume->delete();

    $response = $this->get("/r/{$resume->slug}");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->where('available', false));
});
