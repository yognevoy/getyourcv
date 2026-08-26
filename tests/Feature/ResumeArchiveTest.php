<?php

use App\Actions\Resume\CreateResume;
use App\Enums\ResumeStatus;
use App\Models\Resume;
use App\Models\User;

test('guests are redirected to login when archiving a resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);

    $response = $this->post("/resumes/{$resume->id}/archive");

    $response->assertRedirect(route('login', absolute: false));
});

test('a user cannot archive another user\'s resume', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);

    $response = $this->actingAs($intruder)->post("/resumes/{$resume->id}/archive");

    $response->assertForbidden();
});

test('the owner can archive a resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);

    $response = $this->actingAs($owner)->post("/resumes/{$resume->id}/archive");

    $response->assertRedirect();
    expect(Resume::find($resume->id)->status)->toBe(ResumeStatus::Archived);
});

test('an archived resume is no longer publicly available', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);
    $this->actingAs($owner)->post("/resumes/{$resume->id}/archive");

    $response = $this->get("/r/{$resume->slug}");

    $response->assertInertia(fn ($page) => $page->component('Public/Resume')->where('available', false));
});

test('guests are redirected to login when unarchiving a resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);

    $response = $this->post("/resumes/{$resume->id}/unarchive");

    $response->assertRedirect(route('login', absolute: false));
});

test('a user cannot unarchive another user\'s resume', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);

    $response = $this->actingAs($intruder)->post("/resumes/{$resume->id}/unarchive");

    $response->assertForbidden();
});

test('the owner can unarchive a resume back to draft', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);
    $this->actingAs($owner)->post("/resumes/{$resume->id}/archive");

    $response = $this->actingAs($owner)->post("/resumes/{$resume->id}/unarchive");

    $response->assertRedirect();
    expect(Resume::find($resume->id)->status)->toBe(ResumeStatus::Draft);
});

test('an unarchived resume is publicly available again', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);
    $this->actingAs($owner)->post("/resumes/{$resume->id}/archive");
    $this->actingAs($owner)->post("/resumes/{$resume->id}/unarchive");

    $response = $this->get("/r/{$resume->slug}");

    $response->assertInertia(fn ($page) => $page->component('Public/Resume')->where('available', true));
});
