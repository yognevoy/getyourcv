<?php

use App\Models\User;

test('guests can view the resume creation page', function () {
    $response = $this->get('/resume/new');

    $response->assertStatus(200);
});

test('guests are redirected to login when saving a resume', function () {
    $response = $this->post('/resumes', [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
    ]);

    $response->assertRedirect(route('login', absolute: false));
});

test('authenticated users can save a resume', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/resumes', [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'position' => 'Software Engineer',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertDatabaseHas('resumes', [
        'user_id' => $user->id,
        'full_name' => 'Jane Doe',
    ]);
});
