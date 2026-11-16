<?php

use App\Infrastructure\Spam\CaptchaRulesFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['captcha.driver' => 'turnstile', 'captcha.turnstile.secret' => 'test-secret']);
});

it('rejects malformed captcha values without sending an HTTP request', function (mixed $token) {
    Http::fake();

    $validator = Validator::make(['cf-turnstile-response' => $token], CaptchaRulesFactory::rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('cf-turnstile-response'))->toBe('Captcha invalide');
    Http::assertNothingSent();
})->with([
    'malformed UTF-8' => ["1\x00\xff\xfe%2527%2522"],
    'array' => [['token']],
    'integer' => [123],
]);

it('validates string tokens using the Turnstile response', function (bool $success) {
    Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => $success])]);

    $validator = Validator::make(['cf-turnstile-response' => 'test-token'], CaptchaRulesFactory::rules());

    expect($validator->passes())->toBe($success);
    Http::assertSent(fn ($request) => $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
        && $request['response'] === 'test-token'
        && $request['secret'] === 'test-secret');
})->with([true, false]);
