<?php

use App\Http\Security\ViewerHash;
use Illuminate\Http\Request;

function makeRequest(string $ip, string $userAgent): Request
{
    return Request::create('/', 'GET', server: ['REMOTE_ADDR' => $ip, 'HTTP_USER_AGENT' => $userAgent]);
}

test('the same IP and user agent produce the same hash', function () {
    $request = makeRequest('203.0.113.5', 'TestAgent/1.0');

    expect(ViewerHash::fromRequest($request))->toBe(ViewerHash::fromRequest($request));
});

test('a different user agent produces a different hash', function () {
    $requestA = makeRequest('203.0.113.5', 'TestAgent/1.0');
    $requestB = makeRequest('203.0.113.5', 'TestAgent/2.0');

    expect(ViewerHash::fromRequest($requestA))->not->toBe(ViewerHash::fromRequest($requestB));
});

test('a different IP produces a different hash', function () {
    $requestA = makeRequest('203.0.113.5', 'TestAgent/1.0');
    $requestB = makeRequest('203.0.113.9', 'TestAgent/1.0');

    expect(ViewerHash::fromRequest($requestA))->not->toBe(ViewerHash::fromRequest($requestB));
});

test('the hash is a 64-character hex sha256 digest', function () {
    $request = makeRequest('203.0.113.5', 'TestAgent/1.0');

    expect(ViewerHash::fromRequest($request))->toMatch('/^[a-f0-9]{64}$/');
});
