<?php

use App\Http\Middleware\IdempotencyMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;


it('proceeds normally if no idempotency key is provided', function () {
    // Given
    $middleware = new IdempotencyMiddleware();
    $request = Request::create('/api/v1/orders', 'POST');
    $response = new Response('original response', 200);

    // When
    $result = $middleware->handle($request, function () use ($response) {
        return $response;
    });

    // Then
    expect($result)->toBe($response);
});

it('caches and returns the response if idempotency key is provided for the first time', function () {
    // Given
    $middleware = new IdempotencyMiddleware();
    $request = Request::create('/api/v1/orders', 'POST');
    $request->headers->set('Idempotency-Key', 'test-key-123');
    $response = new Response('created', 201);

    // When
    $result = $middleware->handle($request, function () use ($response) {
        return $response;
    });

    // Then
    expect($result)->toBe($response);
    expect(Cache::get('idempotency_test-key-123'))->toBe($response);
});

it('returns cached response if the same idempotency key is used again', function () {
    // Given
    $middleware = new IdempotencyMiddleware();
    $request1 = Request::create('/api/v1/orders', 'POST');
    $request1->headers->set('Idempotency-Key', 'test-key-456');
    $response1 = new Response('created', 201);

    // Provide the first request
    $middleware->handle($request1, function () use ($response1) {
        return $response1;
    });

    // Provide the second request with the same key
    $request2 = Request::create('/api/v1/orders', 'POST');
    $request2->headers->set('Idempotency-Key', 'test-key-456');

    // When
    $wasCalled = false;
    $result = $middleware->handle($request2, function () use (&$wasCalled) {
        $wasCalled = true;
        return new Response('new response', 200);
    });

    // Then
    expect($wasCalled)->toBeFalse(); // The closure should not be executed again
    expect($result->getContent())->toBe('created');
    expect($result->getStatusCode())->toBe(201);
});
