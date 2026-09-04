<?php

use App\Actions\Resume\CreateResume;
use App\Models\User;

test('viewing a public resume as a guest logs a view', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'published',
    ]);

    $this->get("/r/{$resume->slug}");

    expect($resume->views()->count())->toBe(1);
});

test('viewing your own public resume does not log a view', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'published',
    ]);

    $this->actingAs($owner)->get("/r/{$resume->slug}");

    expect($resume->views()->count())->toBe(0);
});

test('viewing someone else\'s resume while authenticated logs a view', function () {
    $owner = User::factory()->create();
    $visitor = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'published',
    ]);

    $this->actingAs($visitor)->get("/r/{$resume->slug}");

    expect($resume->views()->count())->toBe(1);
});

test('viewing an unavailable resume does not log a view', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'draft',
    ]);

    $this->get("/r/{$resume->slug}");

    expect($resume->views()->count())->toBe(0);
});

test('guests are redirected to login when viewing statistics', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);

    $response = $this->get("/resumes/{$resume->id}/statistics");

    $response->assertRedirect(route('login', absolute: false));
});

test('a user cannot view another user\'s statistics', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, ['title' => 'My Resume', 'full_name' => 'Jane Doe', 'status' => 'draft']);

    $response = $this->actingAs($intruder)->get("/resumes/{$resume->id}/statistics");

    $response->assertForbidden();
});

test('the statistics page reports totals, unique viewers, and a daily breakdown', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'published',
    ]);

    $resume->views()->create(['viewer_hash' => hash('sha256', 'a')]);
    $resume->views()->create(['viewer_hash' => hash('sha256', 'a')]);
    $resume->views()->create(['viewer_hash' => hash('sha256', 'b')]);

    $response = $this->actingAs($owner)->get("/resumes/{$resume->id}/statistics");

    $response->assertInertia(fn ($page) => $page
        ->component('Resume/Statistics')
        ->where('totalViews', 3)
        ->where('uniqueViews', 2)
        ->where('viewsLast7Days', 3)
        ->has('recentViews', 3)
        ->has('dailyViews', 60)
        ->where('dailyViews.59.views', 3)
    );
});
