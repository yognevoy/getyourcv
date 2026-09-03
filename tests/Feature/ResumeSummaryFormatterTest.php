<?php

use App\Actions\Resume\CreateResume;
use App\Models\User;
use App\Services\Ai\Match\ResumeSummaryFormatter;

beforeEach(function () {
    $this->formatter = new ResumeSummaryFormatter;
});

test('formats position, about, skills, and experience bullets into readable lines', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'draft',
        'position' => 'Backend Engineer',
        'about' => 'Builds reliable APIs.',
        'skill_groups' => [
            ['label' => 'Languages', 'skills' => [['value' => 'PHP'], ['value' => 'Go']]],
        ],
        'experiences' => [
            [
                'company' => 'Acme',
                'title' => 'Engineer',
                'bullets' => [
                    ['type' => 'responsibility', 'text' => 'Maintained the billing service'],
                    ['type' => 'achievement', 'text' => 'Cut latency by 30%'],
                ],
            ],
        ],
    ]);

    $summary = $this->formatter->format($resume);

    expect($summary)->toBe(implode("\n", [
        'Target role: Backend Engineer',
        'About: Builds reliable APIs.',
        'Skills: PHP, Go',
        'Engineer at Acme: Maintained the billing service; Cut latency by 30%',
    ]));
});

test('omits sections that have no data instead of leaving them blank', function () {
    $owner = User::factory()->create();
    $resume = app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'draft',
    ]);

    $summary = $this->formatter->format($resume);

    expect($summary)->toBe('');
});
