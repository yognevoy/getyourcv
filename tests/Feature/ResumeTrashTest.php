<?php

use App\Actions\Resume\CreateResume;
use App\Models\Resume;
use App\Models\User;

test('the owner can see their trashed resumes', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $resume->delete();

    $response = $this->actingAs($owner)->get('/trash');

    $response->assertInertia(fn ($page) => $page
        ->component('Resume/Trash')
        ->has('resumes', 1)
        ->where('resumes.0.title', 'My Resume')
    );
});

test('the owner can restore a trashed resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $resume->delete();

    $response = $this->actingAs($owner)->post("/resumes/{$resume->id}/restore");

    $response->assertRedirect();
    expect($resume->fresh()->trashed())->toBeFalse();
});

test('a user cannot restore another user\'s resume', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $resume->delete();

    $response = $this->actingAs($intruder)->post("/resumes/{$resume->id}/restore");

    $response->assertForbidden();
});

test('the owner can permanently delete a trashed resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $resume->delete();

    $response = $this->actingAs($owner)->delete("/resumes/{$resume->id}/force");

    $response->assertRedirect();
    expect(Resume::withTrashed()->find($resume->id))->toBeNull();
});

test('a user cannot permanently delete another user\'s resume', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $resume->delete();

    $response = $this->actingAs($intruder)->delete("/resumes/{$resume->id}/force");

    $response->assertForbidden();
});
