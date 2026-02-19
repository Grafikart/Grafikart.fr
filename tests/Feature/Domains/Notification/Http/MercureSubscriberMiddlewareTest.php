<?php

use App\Models\User;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

it('does not set mercureAuthorization cookie for guests', function () {
    $response = $this->get('/');

    $response->assertCookieMissing('mercureAuthorization');
});

it('sets mercureAuthorization cookie for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertCookie('mercureAuthorization', encrypted: false);

    $token = $response->getCookie('mercureAuthorization', decrypt: false)->getValue();

    $jwtConfiguration = Configuration::forSymmetricSigner(
        new Sha256,
        InMemory::plainText(config('broadcasting.connections.mercure.subscriberSecret'))
    );

    $parsed = $jwtConfiguration->parser()->parse($token);
    $claims = $parsed->claims();

    expect($claims->get('mercure'))->toBe(['subscribe' => ['notification', "notification/{$user->id}"]]);
});
