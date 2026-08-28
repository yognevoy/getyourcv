<?php

use App\Services\Pdf\Mapper\ResumeMapper;

beforeEach(function () {
    $this->mapper = new ResumeMapper;
});

test('minimal data maps to empty contacts and an empty sections object', function () {
    $payload = $this->mapper->mapFromArray(['full_name' => 'Jane Doe']);

    expect($payload['name'])->toBe('Jane Doe');
    expect($payload['position'])->toBe('');
    expect($payload['contacts'])->toBe([]);
    expect($payload['sections'])->toBeInstanceOf(stdClass::class);
    expect((array) $payload['sections'])->toBe([]);
});

test('email becomes a mailto contact and links are mapped by label/url', function () {
    $payload = $this->mapper->mapFromArray([
        'full_name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'links' => [
            ['label' => 'GitHub', 'url' => 'https://github.com/janedoe'],
            ['label' => 'No URL', 'url' => ''],
            ['label' => '', 'url' => 'https://example.com'],
        ],
    ]);

    expect($payload['contacts'])->toBe([
        ['label' => '', 'value' => 'jane@example.com', 'url' => 'mailto:jane@example.com'],
        ['label' => 'GitHub', 'value' => '', 'url' => 'https://github.com/janedoe'],
    ]);
});

test('a blank row just added to a repeatable list is skipped, not rendered empty', function () {
    // ResumeForm.vue pushes an empty {company: '', title: '', bullets: []} the instant
    // "Add experience" is clicked - it must not show up as an empty block in the preview.
    $payload = $this->mapper->mapFromArray([
        'full_name' => 'Jane Doe',
        'experiences' => [
            ['company' => '', 'title' => '', 'bullets' => [['type' => 'responsibility', 'text' => '']]],
            ['company' => 'Acme', 'title' => '', 'bullets' => []],
        ],
    ]);

    $entries = $payload['sections']->experience['entries'];

    expect($entries)->toHaveCount(1);
    expect($entries[0]['company'])->toBe('Acme');
});

test('skill groups join their values into a single comma-separated string', function () {
    $payload = $this->mapper->mapFromArray([
        'full_name' => 'Jane Doe',
        'skill_groups' => [
            ['label' => 'Languages', 'skills' => [['value' => 'PHP'], ['value' => 'JavaScript']]],
        ],
    ]);

    expect($payload['sections']->skills)->toBe([
        'title' => 'Skills',
        'skills' => [['label' => 'Languages', 'value' => 'PHP, JavaScript']],
    ]);
});

test('bullets are split into Responsibilities/Achievements groups, empty groups omitted', function () {
    $payload = $this->mapper->mapFromArray([
        'full_name' => 'Jane Doe',
        'experiences' => [[
            'company' => 'Acme',
            'title' => 'Engineer',
            'is_current' => true,
            'bullets' => [
                ['type' => 'responsibility', 'text' => 'Built APIs.'],
            ],
        ]],
    ]);

    $entry = $payload['sections']->experience['entries'][0];

    expect($entry['groups'])->toBe([
        ['label' => 'Responsibilities', 'bullets' => ['Built APIs.']],
    ]);
});

test('period formatting combines month/year with an em dash, or falls back to Present', function () {
    $payload = $this->mapper->mapFromArray([
        'full_name' => 'Jane Doe',
        'experiences' => [
            ['company' => 'A', 'title' => 'X', 'period_from' => '2023-03-15', 'is_current' => true, 'bullets' => []],
            ['company' => 'B', 'title' => 'Y', 'period_from' => '2022-06-01', 'period_to' => '2023-03-01', 'bullets' => []],
            ['company' => 'C', 'title' => 'Z', 'bullets' => []],
        ],
    ]);

    $entries = $payload['sections']->experience['entries'];

    expect($entries[0]['period'])->toBe('March 2023 — Present');
    expect($entries[1]['period'])->toBe('June 2022 — March 2023');
    expect($entries[2]['period'])->toBe('');
});

test('a section is omitted entirely when it has no usable data', function () {
    $payload = $this->mapper->mapFromArray([
        'full_name' => 'Jane Doe',
        'about' => '',
        'skill_groups' => [['label' => '', 'skills' => []]],
        'educations' => [],
    ]);

    expect((array) $payload['sections'])->toBe([]);
});
