<?php

use App\Services\Ai\RewriteTarget;
use App\Services\Ai\RewriteTarget\About;
use App\Services\Ai\RewriteTarget\Achievement;
use App\Services\Ai\RewriteTarget\Responsibility;

test('fromValue resolves the matching concrete target', function () {
    expect(RewriteTarget::fromValue('about'))->toBeInstanceOf(About::class);
    expect(RewriteTarget::fromValue('responsibility'))->toBeInstanceOf(Responsibility::class);
    expect(RewriteTarget::fromValue('achievement'))->toBeInstanceOf(Achievement::class);
});

test('fromValue rejects an unknown value', function () {
    RewriteTarget::fromValue('not-a-real-target');
})->throws(InvalidArgumentException::class);

test('values lists every known target value', function () {
    expect(RewriteTarget::values())->toBe(['about', 'responsibility', 'achievement']);
});

test('each target reports its own value and prompt subject', function () {
    expect((new About)->value())->toBe('about');
    expect((new Responsibility)->value())->toBe('responsibility');
    expect((new Achievement)->value())->toBe('achievement');

    expect((new About)->subject())->toContain('"About" section');
    expect((new Responsibility)->subject())->toContain('"Responsibilities" bullet point');
    expect((new Achievement)->subject())->toContain('"Achievements" bullet point');
});
