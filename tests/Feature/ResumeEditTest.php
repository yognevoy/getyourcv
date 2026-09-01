<?php

use App\Actions\Resume\CreateResume;
use App\Models\User;

test('guests are redirected to login when opening the edit page', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'draft',
    ]);

    $response = $this->get("/resumes/{$resume->id}/edit");

    $response->assertRedirect(route('login', absolute: false));
});

test('a user cannot open another user\'s edit page', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'draft',
    ]);

    $response = $this->actingAs($intruder)->get("/resumes/{$resume->id}/edit");

    $response->assertForbidden();
});

test('the owner can open the edit page with the resume data', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'draft',
        'position' => 'Software Engineer',
        'links' => [['label' => 'GitHub', 'url' => 'https://github.com/jane']],
    ]);

    $response = $this->actingAs($owner)->get("/resumes/{$resume->id}/edit");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Resume/Edit')
        ->where('resume.full_name', 'Jane Doe')
        ->where('resume.position', 'Software Engineer')
        ->where('resume.links.0.label', 'GitHub')
    );
});

test('the owner can update a resume', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'draft',
    ]);

    $response = $this->actingAs($owner)->put("/resumes/{$resume->id}", [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'position' => 'Senior Engineer',
        'status' => 'published',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertDatabaseHas('resumes', [
        'id' => $resume->id,
        'position' => 'Senior Engineer',
    ]);
});
