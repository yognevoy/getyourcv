<?php

use App\Services\Ai\Exceptions\AiServiceException;
use App\Services\Ai\Rewrite\RewriteVariantsSchema;

beforeEach(function () {
    $this->schema = new RewriteVariantsSchema;
});

test('toolDefinition requires exactly the three variant keys', function () {
    $definition = $this->schema->toolDefinition();

    expect($definition['function']['name'])->toBe($this->schema->toolName());
    expect($definition['function']['parameters']['required'])->toBe(['shorter', 'stronger', 'with_numbers']);
    expect(array_keys($definition['function']['parameters']['properties']))->toBe(['shorter', 'stronger', 'with_numbers']);
});

test('parse accepts a well-formed JSON arguments string', function () {
    $variants = $this->schema->parse(json_encode([
        'shorter' => 'a', 'stronger' => 'b', 'with_numbers' => 'c',
    ]));

    expect($variants)->toBe(['shorter' => 'a', 'stronger' => 'b', 'with_numbers' => 'c']);
});

test('parse rejects a non-string argument', function () {
    $this->schema->parse(['shorter' => 'a']);
})->throws(AiServiceException::class, 'AI service returned an unexpected response.');

test('parse rejects malformed JSON', function () {
    $this->schema->parse('not json');
})->throws(AiServiceException::class, 'AI service returned an unexpected response.');

test('parse rejects JSON missing a required key', function () {
    $this->schema->parse(json_encode(['shorter' => 'a', 'stronger' => 'b']));
})->throws(AiServiceException::class, 'AI service returned an unexpected response.');
