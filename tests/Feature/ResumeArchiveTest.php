<?php

use App\Actions\Resume\CreateResume;
use App\Models\Resume;
use App\Models\User;

test('guests are redirected to login when archiving a resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);

    $response = $this->patch("/resumes/{$resume->id}/status", ['status' => 'archived']);

    $response->assertRedirect(route('login', absolute: false));
});

test('a user cannot archive another user\'s resume', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);

    $response = $this->actingAs($intruder)->patch("/resumes/{$resume->id}/status", ['status' => 'archived']);

    $response->assertForbidden();
});

test('the owner can archive a resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);

    $response = $this->actingAs($owner)->patch("/resumes/{$resume->id}/status", ['status' => 'archived']);

    $response->assertRedirect();
    expect(Resume::find($resume->id)->status)->toBe(\App\Enums\ResumeStatus::Archived);
});

test('an archived resume is no longer publicly available', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);
    $this->actingAs($owner)->patch("/resumes/{$resume->id}/status", ['status' => 'archived']);

    $response = $this->get("/r/{$resume->slug}");

    $response->assertInertia(fn ($page) => $page->component('Public/Resume')->where('available', false));
});
