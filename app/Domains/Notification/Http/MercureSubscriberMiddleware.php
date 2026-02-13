<?php

namespace App\Domains\Notification\Http;

use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\HttpFoundation\Response;

class MercureSubscriberMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->user()) {
            return $response;
        }

        $jwtConfiguration = Configuration::forSymmetricSigner(
            new Sha256,
            InMemory::plainText(config('broadcasting.connections.mercure.subscriberSecret'))
        );

        $token = $jwtConfiguration->builder()
            ->withClaim('mercure', ['subscribe' => ['notification']])
            ->getToken($jwtConfiguration->signer(), $jwtConfiguration->signingKey())
            ->toString();

        $response->headers->setCookie(cookie(
            name: 'mercureAuthorization',
            value: $token,
            path: '/.well-known/mercure',
            httpOnly: true,
        ));

        return $response;
    }
}
