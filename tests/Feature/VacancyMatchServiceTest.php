<?php

use App\Services\Ai\Exceptions\AiServiceException;
use App\Services\Ai\Match\VacancyMatchService;
use App\Services\Ai\OpenAiChatCompletionsClient;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->service = new VacancyMatchService(new OpenAiChatCompletionsClient(
        baseUrl: 'https://api.example.com/v1',
        apiKey: 'sk-test',
        model: 'test-model',
    ));
});

test('match sends the vacancy text as the user message and parses a successful response', function () {
    Http::fake([
        '*' => Http::response([
            'choices' => [[
                'message' => [
                    'tool_calls' => [[
                        'function' => ['arguments' => json_encode([
                            'score' => 80,
                            'matched_skills' => ['PHP'],
                            'missing_skills' => ['Go'],
                            'summary' => 'Good fit.',
                        ])],
                    ]],
                ],
            ]],
        ]),
    ]);

    $result = $this->service->match('Skills: PHP, Laravel', 'We need a Go and PHP engineer.');

    expect($result)->toBe([
        'score' => 80,
        'matched_skills' => ['PHP'],
        'missing_skills' => ['Go'],
        'summary' => 'Good fit.',
    ]);

    Http::assertSent(function ($request) {
        return $request['tool_choice']['function']['name'] === 'submit_vacancy_match'
            && $request['messages'][1] === ['role' => 'user', 'content' => 'We need a Go and PHP engineer.'];
    });
});

test('match wraps a rate-limited response into a friendly AiServiceException', function () {
    Http::fake(['*' => Http::response(['error' => 'rate limited'], 429)]);

    $this->service->match('Skills: PHP', 'A vacancy.');
})->throws(AiServiceException::class, 'AI service rate limit reached. Please try again in a moment.');
