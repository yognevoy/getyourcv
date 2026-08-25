<?php

use App\Actions\Resume\CreateResume;
use App\Models\Resume;
use App\Models\User;

test('guests are redirected to login when duplicating a resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
    ]);

    $response = $this->post("/resumes/{$resume->id}/duplicate");

    $response->assertRedirect(route('login', absolute: false));
});

test('a user cannot duplicate another user\'s resume', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
    ]);

    $response = $this->actingAs($intruder)->post("/resumes/{$resume->id}/duplicate");

    $response->assertForbidden();
});

test('the owner can duplicate a resume and is redirected to editing the copy', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'links' => [['label' => 'GitHub', 'url' => 'https://github.com/jane']],
    ]);

    $response = $this->actingAs($owner)->post("/resumes/{$resume->id}/duplicate");

    $clone = Resume::where('id', '!=', $resume->id)->where('user_id', $owner->id)->firstOrFail();

    $response->assertRedirect(route('resumes.edit', $clone, absolute: false));
    expect($clone->title)->toBe('Copy of My Resume');
    expect($clone->full_name)->toBe('Jane Doe');
    expect($clone->links)->toHaveCount(1);
});
