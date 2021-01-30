<?php

namespace App\Http\Twig;

use App\Domain\Auth\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigThemeExtension extends AbstractExtension
{
    private Security $security;
    private RequestStack $requestStack;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('body_theme', [$this, 'getUserTheme']),
        ];
    }

    public function getUserTheme(): string
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $theme = $user->getTheme();
        } else {
            $request = $this->requestStack->getCurrentRequest();
            $theme = $request ? $request->cookies->get('theme') : null;
        }
        if ($theme) {
            return "theme-$theme";
        }

        return '';
    }
}
