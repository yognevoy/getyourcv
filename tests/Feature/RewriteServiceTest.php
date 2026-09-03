<?php

use App\Services\Ai\Exceptions\AiServiceException;
use App\Services\Ai\OpenAiChatCompletionsClient;
use App\Services\Ai\Rewrite\RewriteService;
use App\Services\Ai\Rewrite\RewriteTarget\About;
use App\Services\Ai\Rewrite\RewriteTarget\Achievement;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->service = new RewriteService(new OpenAiChatCompletionsClient(
        baseUrl: 'https://api.example.com/v1',
        apiKey: 'sk-test',
        model: 'test-model',
    ));

    $this->toolResponse = fn (array $variants) => [
        'choices' => [[
            'message' => [
                'tool_calls' => [[
                    'function' => ['arguments' => json_encode($variants)],
                ]],
            ],
        ]],
    ];
});

test('a successful response is parsed into three variants', function () {
    Http::fake([
        'api.example.com/*' => Http::response(($this->toolResponse)([
            'shorter' => 'Short version.',
            'stronger' => 'Strong version.',
            'with_numbers' => 'Version with 42%.',
        ])),
    ]);

    $variants = $this->service->rewrite('Built things.', new Achievement);

    expect($variants)->toBe([
        'shorter' => 'Short version.',
        'stronger' => 'Strong version.',
        'with_numbers' => 'Version with 42%.',
    ]);
});

test('the request targets chat/completions with a bearer token, the configured model, and a forced tool call', function () {
    Http::fake([
        '*' => Http::response(($this->toolResponse)([
            'shorter' => 'a', 'stronger' => 'b', 'with_numbers' => 'c',
        ])),
    ]);

    $this->service->rewrite('Built things.', new About);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.example.com/v1/chat/completions'
            && $request->hasHeader('Authorization', 'Bearer sk-test')
            && $request['model'] === 'test-model'
            && $request['tool_choice']['function']['name'] === 'submit_rewrite_variants'
            && $request['messages'][1] === ['role' => 'user', 'content' => 'Built things.'];
    });
});

test('a rate-limited response throws a friendly AiServiceException', function () {
    Http::fake(['*' => Http::response(['error' => 'rate limited'], 429)]);

    $this->service->rewrite('Built things.', new Achievement);
})->throws(AiServiceException::class, 'AI service rate limit reached. Please try again in a moment.');

test('a response missing a required variant throws AiServiceException', function () {
    Http::fake([
        '*' => Http::response(($this->toolResponse)(['shorter' => 'a', 'stronger' => 'b'])),
    ]);

    $this->service->rewrite('Built things.', new Achievement);
})->throws(AiServiceException::class, 'AI service returned an unexpected response.');

test('a connection failure is wrapped into AiServiceException', function () {
    Http::fake(function () {
        throw new ConnectionException('timed out');
    });

    $this->service->rewrite('Built things.', new Achievement);
})->throws(AiServiceException::class, 'AI service is unreachable. Please try again later.');
