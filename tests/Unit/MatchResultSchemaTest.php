<?php

use App\Services\Ai\Exceptions\AiServiceException;
use App\Services\Ai\Match\MatchResultSchema;

beforeEach(function () {
    $this->schema = new MatchResultSchema;
});

test('toolDefinition requires exactly the four result keys', function () {
    $definition = $this->schema->toolDefinition();

    expect($definition['function']['name'])->toBe($this->schema->toolName());
    expect($definition['function']['parameters']['required'])
        ->toBe(['score', 'matched_skills', 'missing_skills', 'summary']);
});

test('parse accepts a well-formed JSON arguments string', function () {
    $result = $this->schema->parse(json_encode([
        'score' => 72,
        'matched_skills' => ['PHP', 'Laravel'],
        'missing_skills' => ['Kubernetes'],
        'summary' => 'Strong backend fit.',
    ]));

    expect($result)->toBe([
        'score' => 72,
        'matched_skills' => ['PHP', 'Laravel'],
        'missing_skills' => ['Kubernetes'],
        'summary' => 'Strong backend fit.',
    ]);
});

test('parse clamps an out-of-range score into 0-100', function () {
    $result = $this->schema->parse(json_encode([
        'score' => 142,
        'matched_skills' => [],
        'missing_skills' => [],
        'summary' => 'x',
    ]));

    expect($result['score'])->toBe(100);

    $result = $this->schema->parse(json_encode([
        'score' => -5,
        'matched_skills' => [],
        'missing_skills' => [],
        'summary' => 'x',
    ]));

    expect($result['score'])->toBe(0);
});

test('parse rejects a non-string argument', function () {
    $this->schema->parse(['score' => 1]);
})->throws(AiServiceException::class, 'AI service returned an unexpected response.');

test('parse rejects malformed JSON', function () {
    $this->schema->parse('not json');
})->throws(AiServiceException::class, 'AI service returned an unexpected response.');

test('parse rejects JSON missing a required key', function () {
    $this->schema->parse(json_encode(['score' => 1, 'matched_skills' => [], 'missing_skills' => []]));
})->throws(AiServiceException::class, 'AI service returned an unexpected response.');

test('parse rejects skill lists that are not arrays', function () {
    $this->schema->parse(json_encode([
        'score' => 1, 'matched_skills' => 'PHP', 'missing_skills' => [], 'summary' => 'x',
    ]));
})->throws(AiServiceException::class, 'AI service returned an unexpected response.');
