<?php

namespace App\Http\ParamConverter;

use App\Domain\Password\Entity\PasswordResetToken;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class PasswordResetTokenParamConverter implements ParamConverterInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $token = $this->em->getRepository(PasswordResetToken::class)->findOneBy([
            'token' => $request->get('token'),
        ]);
        $request->attributes->set('token', $token);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        return PasswordResetToken::class === $configuration->getClass() && 'token' === $configuration->getName();
    }
}
