<?php

namespace App\Http\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigRepositoryExtension extends AbstractExtension
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('repository', [$this, 'repositoryCall']),
        ];
    }

    /**
     * @param class-string<object> $repositoryClass
     *
     * @return mixed
     */
    public function repositoryCall(string $repositoryClass, string $method, array $params = [])
    {
        $repository = $this->em->getRepository($repositoryClass);
        /** @var callable $callable */
        $callable = [$repository, $method];

        return call_user_func_array($callable, $params);
    }
}
