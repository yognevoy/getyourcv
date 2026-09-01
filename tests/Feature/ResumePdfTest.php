<?php

use App\Actions\Resume\CreateResume;
use App\Models\User;

test('guests are redirected to login when downloading a resume PDF', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);

    $response = $this->get(route('resumes.pdf', $resume));

    $response->assertRedirect(route('login'));
});

test('a user cannot download another user\'s resume PDF', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);

    $response = $this->actingAs($other)->get(route('resumes.pdf', $resume));

    $response->assertForbidden();
});

test('the owner can download their resume PDF', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);

    $response = $this->actingAs($owner)->get(route('resumes.pdf', $resume));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('a guest can view the cached PDF for a public resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'published',
    ]);

    $response = $this->get(route('resumes.public-file', $resume->slug));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('a draft resume PDF is not publicly reachable', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);

    $response = $this->get(route('resumes.public-file', $resume->slug));

    $response->assertNotFound();
});

test('a trashed resume PDF is not publicly reachable', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $resume->delete();

    $response = $this->get(route('resumes.public-file', $resume->slug));

    $response->assertNotFound();
});
