<?php

use App\Services\Ai\Rewrite\RewritePromptBuilder;
use App\Services\Ai\Rewrite\RewriteTarget\About;
use App\Services\Ai\Rewrite\RewriteTarget\Achievement;
use App\Services\Ai\Rewrite\RewriteTarget\Responsibility;

beforeEach(function () {
    $this->builder = new RewritePromptBuilder;
});

test('the prompt instructs the model to call the given tool name', function () {
    $prompt = $this->builder->build(new Achievement, 'submit_rewrite_variants');

    expect($prompt)->toContain('Call the submit_rewrite_variants tool');
});

test('the prompt describes each target differently', function () {
    $about = $this->builder->build(new About, 'x');
    $responsibility = $this->builder->build(new Responsibility, 'x');
    $achievement = $this->builder->build(new Achievement, 'x');

    expect($about)->toContain('"About" section');
    expect($responsibility)->toContain('"Responsibilities" bullet point');
    expect($achievement)->toContain('"Achievements" bullet point');
    expect($about)->not->toBe($responsibility);
    expect($responsibility)->not->toBe($achievement);
});
