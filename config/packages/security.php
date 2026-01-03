<?php

declare(strict_types=1);

use App\Domain\Auth\Authenticator;
use App\Domain\Auth\Security\UserChecker;
use App\Domain\Auth\User;
use App\Http\Security\AccessDeniedHandler;
use App\Http\Security\AuthenticationEntryPoint;
use App\Infrastructure\Social\Authenticator\FacebookAuthenticator;
use App\Infrastructure\Social\Authenticator\GithubAuthenticator;
use App\Infrastructure\Social\Authenticator\GoogleAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => 'auto',
            User::class => [
                'algorithm' => 'auto',
            ],
        ],
        'providers' => [
            'app_user_provider' => [
                'entity' => [
                    'class' => User::class,
                    'property' => 'email',
                ],
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'entry_point' => AuthenticationEntryPoint::class,
                'lazy' => true,
                'user_checker' => UserChecker::class,
                'switch_user' => [
                    'role' => 'CAN_SWITCH_USER',
                    'parameter' => '_ninja',
                ],
                'custom_authenticator' => [
                    Authenticator::class,
                    GithubAuthenticator::class,
                    GoogleAuthenticator::class,
                    FacebookAuthenticator::class,
                ],
                'logout' => [
                    'path' => 'auth_logout',
                ],
                'remember_me' => [
                    'secret' => '%kernel.secret%',
                    'lifetime' => 604800,
                    'path' => '/',
                    'samesite' => 'strict',
                ],
                'access_denied_handler' => AccessDeniedHandler::class,
            ],
        ],
        'access_control' => null,
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('security', [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'auto',
                    'cost' => 4,
                    'time_cost' => 3,
                    'memory_cost' => 10,
                ],
            ],
        ]);
    }
};
