<?php

namespace App\Core\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigRepositoryExtension extends AbstractExtension
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('repository', [$this, 'repositoryCall']),
        ];
    }

    /**
     * @param class-string<mixed> $repositoryClass
     * @param string $method
     * @param array $params
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
