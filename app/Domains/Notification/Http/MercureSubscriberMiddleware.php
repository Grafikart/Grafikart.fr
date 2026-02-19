<?php

namespace App\Domains\Notification\Http;

use App\Models\User;
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
        $user = $request->user();
        if (! ($user instanceof User)) {
            return $response;
        }

        $jwtConfiguration = Configuration::forSymmetricSigner(
            new Sha256,
            InMemory::plainText(config('broadcasting.connections.mercure.subscriberSecret'))
        );

        $token = $jwtConfiguration->builder()
            ->withClaim('mercure', ['subscribe' => ['notification', sprintf('notification/%s', $user->id)]])
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
