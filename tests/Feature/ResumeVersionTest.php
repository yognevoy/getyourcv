<?php

use App\Actions\Resume\CreateResume;
use App\Actions\Resume\UpdateResume;
use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\User;

test('creating a resume records its first version', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);

    expect($resume->versions()->count())->toBe(1);
});

test('updating a resume records a new version', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);

    app(UpdateResume::class)->execute($resume, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft', 'about' => 'Updated']);

    expect($resume->versions()->count())->toBe(2);
});

test('guests are redirected to login when listing versions', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);

    $response = $this->get("/resumes/{$resume->id}/versions");

    $response->assertRedirect(route('login', absolute: false));
});

test('a user cannot list another user\'s versions', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);

    $response = $this->actingAs($intruder)->get("/resumes/{$resume->id}/versions");

    $response->assertForbidden();
});

test('the owner can list versions with the newest marked as current', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    app(UpdateResume::class)->execute($resume, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft', 'about' => 'Updated']);

    $response = $this->actingAs($owner)->getJson("/resumes/{$resume->id}/versions");

    $response->assertOk();
    $versions = $response->json('versions');
    expect($versions)->toHaveCount(2);
    expect($versions[0]['is_current'])->toBeTrue();
    expect($versions[1]['is_current'])->toBeFalse();
});

test('the owner can view a past version as a PDF', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $version = $resume->versions()->firstOrFail();

    $response = $this->actingAs($owner)->get("/resumes/{$resume->id}/versions/{$version->id}/pdf");

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('a version pdf from another resume 404s', function () {
    $owner = User::factory()->create();
    $resumeA = app(CreateResume::class)->execute($owner, ['title' => 'Resume A', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $resumeB = app(CreateResume::class)->execute($owner, ['title' => 'Resume B', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $versionOfA = $resumeA->versions()->firstOrFail();

    $response = $this->actingAs($owner)->get("/resumes/{$resumeB->id}/versions/{$versionOfA->id}/pdf");

    $response->assertNotFound();
});

test('the owner can restore a resume to a past version', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft', 'about' => 'Original']);
    $original = $resume->versions()->firstOrFail();
    app(UpdateResume::class)->execute($resume, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft', 'about' => 'Changed']);

    $response = $this->actingAs($owner)->post("/resumes/{$resume->id}/versions/{$original->id}/restore");

    $response->assertRedirect();
    expect(Resume::find($resume->id)->about)->toBe('Original');
    expect(Resume::find($resume->id)->current_version_id)->toBe($original->id);
    // Restoring doesn't create a new version - it just moves the pointer.
    expect($resume->versions()->count())->toBe(2);
});

test('restoring an older version moves it to the front of the list as current', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $original = $resume->versions()->firstOrFail();
    app(UpdateResume::class)->execute($resume, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft', 'about' => 'Changed']);

    $this->actingAs($owner)->post("/resumes/{$resume->id}/versions/{$original->id}/restore");

    $response = $this->actingAs($owner)->getJson("/resumes/{$resume->id}/versions");

    $versions = $response->json('versions');
    expect($versions[0]['id'])->toBe($original->id);
    expect($versions[0]['is_current'])->toBeTrue();
    expect($versions[1]['is_current'])->toBeFalse();
});

test('a user cannot restore another user\'s resume version', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $version = $resume->versions()->firstOrFail();

    $response = $this->actingAs($intruder)->post("/resumes/{$resume->id}/versions/{$version->id}/restore");

    $response->assertForbidden();
});

test('the owner can delete a past version', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $first = $resume->versions()->firstOrFail();
    app(UpdateResume::class)->execute($resume, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft', 'about' => 'Changed']);

    $response = $this->actingAs($owner)->delete("/resumes/{$resume->id}/versions/{$first->id}");

    $response->assertRedirect();
    expect(ResumeVersion::find($first->id))->toBeNull();
});

test('the current version cannot be deleted', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $current = $resume->versions()->firstOrFail();

    $response = $this->actingAs($owner)->delete("/resumes/{$resume->id}/versions/{$current->id}");

    $response->assertStatus(422);
    expect(ResumeVersion::find($current->id))->not->toBeNull();
});

test('deleting a resume permanently cascades to its versions', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);
    $resumeId = $resume->id;

    $resume->forceDelete();

    expect(ResumeVersion::where('resume_id', $resumeId)->count())->toBe(0);
});
