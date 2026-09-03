<?php

use App\Actions\Resume\CreateResume;
use App\Models\Resume;
use App\Models\User;
use App\Services\Ai\Exceptions\AiServiceException;
use App\Services\Ai\Match\MatchServiceInterface;

function fakeAiMatchService(array $result, ?int &$callCount = null): MatchServiceInterface
{
    $callCount = 0;

    return new class($result, $callCount) implements MatchServiceInterface
    {
        public function __construct(private readonly array $result, private int &$callCount) {}

        public function match(string $resumeSummary, string $vacancyText): array
        {
            $this->callCount++;

            return $this->result;
        }
    };
}

function createResumeWithSkill(User $owner): Resume
{
    return app(CreateResume::class)->execute($owner, [
        'title' => 'My Resume',
        'full_name' => 'Jane Doe',
        'status' => 'draft',
        'skill_groups' => [
            ['label' => 'Languages', 'skills' => [['value' => 'PHP']]],
        ],
    ]);
}

test('guests are redirected to login when opening the match page', function () {
    $owner = User::factory()->create();
    $resume = createResumeWithSkill($owner);

    $response = $this->get("/resumes/{$resume->id}/match");

    $response->assertRedirect(route('login', absolute: false));
});

test('a user cannot open another user\'s match page', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = createResumeWithSkill($owner);

    $response = $this->actingAs($intruder)->get("/resumes/{$resume->id}/match");

    $response->assertForbidden();
});

test('the owner can analyze a vacancy and sees the result in history', function () {
    $owner = User::factory()->create();
    $resume = createResumeWithSkill($owner);

    $this->app->instance(MatchServiceInterface::class, fakeAiMatchService([
        'score' => 75,
        'matched_skills' => ['PHP'],
        'missing_skills' => ['Go'],
        'summary' => 'Solid backend match.',
    ]));

    $response = $this->actingAs($owner)->post("/resumes/{$resume->id}/match", [
        'vacancy_title' => 'Backend Engineer',
        'vacancy_text' => str_repeat('We need a PHP and Go engineer. ', 3),
    ]);

    $response->assertRedirect();
    expect($resume->vacancyMatches()->count())->toBe(1);

    $match = $resume->vacancyMatches()->first();
    expect($match->vacancy_title)->toBe('Backend Engineer');
    expect($match->score)->toBe(75);
    expect($match->matched_skills)->toBe(['PHP']);
    expect($match->missing_skills)->toBe(['Go']);
});

test('submitting the same vacancy text again reuses the stored match instead of calling the AI again', function () {
    $owner = User::factory()->create();
    $resume = createResumeWithSkill($owner);

    $service = fakeAiMatchService([
        'score' => 60,
        'matched_skills' => [],
        'missing_skills' => [],
        'summary' => 'x',
    ], $callCount);
    $this->app->instance(MatchServiceInterface::class, $service);

    $vacancyText = str_repeat('Same vacancy text. ', 3);

    $this->actingAs($owner)->post("/resumes/{$resume->id}/match", ['vacancy_text' => $vacancyText]);
    $this->actingAs($owner)->post("/resumes/{$resume->id}/match", ['vacancy_text' => $vacancyText]);

    expect($resume->vacancyMatches()->count())->toBe(1);
});

test('a failing AI service redirects back with an error instead of a crash', function () {
    $owner = User::factory()->create();
    $resume = createResumeWithSkill($owner);

    $this->app->instance(MatchServiceInterface::class, new class implements MatchServiceInterface
    {
        public function match(string $resumeSummary, string $vacancyText): array
        {
            throw new AiServiceException('AI service timed out. Please try again.');
        }
    });

    $response = $this->actingAs($owner)->post("/resumes/{$resume->id}/match", [
        'vacancy_text' => str_repeat('A vacancy. ', 3),
    ]);

    $response->assertSessionHasErrors('vacancy_text');
    expect($resume->vacancyMatches()->count())->toBe(0);
});

test('vacancy_text is required and must be at least 30 characters', function () {
    $owner = User::factory()->create();
    $resume = createResumeWithSkill($owner);

    $response = $this->actingAs($owner)->post("/resumes/{$resume->id}/match", ['vacancy_text' => 'too short']);

    $response->assertSessionHasErrors('vacancy_text');
});
