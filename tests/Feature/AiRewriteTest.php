<?php

use App\Services\Ai\Exceptions\AiServiceException;
use App\Services\Ai\Rewrite\RewriteServiceInterface;
use App\Services\Ai\Rewrite\RewriteTarget;
use App\Services\Ai\Rewrite\RewriteTarget\Achievement;

test('a guest can rewrite text into three variants', function () {
    $this->app->instance(RewriteServiceInterface::class, new class implements RewriteServiceInterface
    {
        public function rewrite(string $text, RewriteTarget $target): array
        {
            expect($text)->toBe('Built APIs.');
            expect($target)->toBeInstanceOf(Achievement::class);

            return ['shorter' => 'APIs.', 'stronger' => 'Built robust APIs.', 'with_numbers' => 'Built 5 APIs.'];
        }
    });

    $response = $this->postJson('/ai/rewrite', ['text' => 'Built APIs.', 'target' => 'achievement']);

    $response->assertOk();
    $response->assertJson([
        'variants' => [
            'shorter' => 'APIs.',
            'stronger' => 'Built robust APIs.',
            'with_numbers' => 'Built 5 APIs.',
        ],
    ]);
});

test('a failing AI service turns into a 503 with a message, not a crash', function () {
    $this->app->instance(RewriteServiceInterface::class, new class implements RewriteServiceInterface
    {
        public function rewrite(string $text, RewriteTarget $target): array
        {
            throw new AiServiceException('AI service rate limit reached. Please try again in a moment.');
        }
    });

    $response = $this->postJson('/ai/rewrite', ['text' => 'Built APIs.', 'target' => 'achievement']);

    $response->assertStatus(503);
    $response->assertJson(['message' => 'AI service rate limit reached. Please try again in a moment.']);
});

test('text is required', function () {
    $response = $this->postJson('/ai/rewrite', ['target' => 'achievement']);

    $response->assertJsonValidationErrors('text');
});

test('target must be a known rewrite target', function () {
    $response = $this->postJson('/ai/rewrite', ['text' => 'Built APIs.', 'target' => 'not-a-real-target']);

    $response->assertJsonValidationErrors('target');
});
