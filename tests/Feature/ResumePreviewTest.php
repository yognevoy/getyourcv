<?php

test('a guest can generate a live PDF preview from unsaved form data', function () {
    $response = $this->post('/resume-preview', [
        'title' => 'Draft',
        'full_name' => 'Jane Doe',
        'position' => 'Backend Developer',
        'email' => 'jane@example.com',
        'about' => 'Backend engineer.',
        'links' => [['label' => 'GitHub', 'url' => 'https://github.com/janedoe']],
        'skill_groups' => [['label' => 'Languages', 'skills' => [['value' => 'PHP']]]],
        'experiences' => [[
            'company' => 'Acme',
            'title' => 'Developer',
            'is_current' => true,
            'bullets' => [['type' => 'responsibility', 'text' => 'Built APIs.']],
        ]],
    ]);

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
    expect(substr($response->getContent(), 0, 4))->toBe('%PDF');
});

test('preview does not require the internal title field', function () {
    $response = $this->post('/resume-preview', [
        'full_name' => 'Jane Doe',
    ]);

    $response->assertOk();
});

test('preview tolerates a blank full name', function () {
    // The form has no other signal it's "empty" to fall back on - the live preview
    // component itself skips the request client-side, but the endpoint must still
    // accept this shape rather than 422, since nested rows (see below) share the
    // same "everything is optional" contract.
    $response = $this->post('/resume-preview', [
        'full_name' => '',
    ]);

    $response->assertOk();
});

test('preview tolerates a blank row just added to a repeatable list', function () {
    // ResumeForm.vue pushes {label: '', url: ''} etc. the instant "Add ..." is clicked,
    // before the user types anything - this must not 422 the whole preview.
    $response = $this->post('/resume-preview', [
        'full_name' => 'Jane Doe',
        'links' => [['label' => '', 'url' => '']],
        'skill_groups' => [['label' => '', 'skills' => [['value' => '']]]],
        'experiences' => [['company' => '', 'title' => '', 'bullets' => [['type' => 'responsibility', 'text' => '']]]],
    ]);

    $response->assertOk();
});

test('preview still rejects genuinely invalid data', function () {
    $response = $this->post('/resume-preview', [
        'full_name' => 'Jane Doe',
        'experiences' => [['bullets' => [['type' => 'not-a-real-type', 'text' => 'x']]]],
    ]);

    $response->assertSessionHasErrors('experiences.0.bullets.0.type');
});
