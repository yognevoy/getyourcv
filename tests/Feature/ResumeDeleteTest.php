<?php

use App\Actions\Resume\CreateResume;
use App\Models\Resume;
use App\Models\User;

test('guests are redirected to login when deleting a resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);

    $response = $this->delete("/resumes/{$resume->id}");

    $response->assertRedirect(route('login', absolute: false));
});

test('a user cannot delete another user\'s resume', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);

    $response = $this->actingAs($intruder)->delete("/resumes/{$resume->id}");

    $response->assertForbidden();
});

test('the owner can soft delete a resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);

    $response = $this->actingAs($owner)->delete("/resumes/{$resume->id}");

    $response->assertRedirect();
    expect(Resume::withTrashed()->find($resume->id)->trashed())->toBeTrue();
});

test('a soft deleted resume no longer appears on the dashboard', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe']);
    $resume->delete();

    $response = $this->actingAs($owner)->get('/dashboard');

    $response->assertInertia(fn ($page) => $page->component('Dashboard')->has('resumes', 0));
});
