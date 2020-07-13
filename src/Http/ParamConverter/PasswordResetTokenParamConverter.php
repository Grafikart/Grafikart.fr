<?php

namespace App\Http\ParamConverter;

use App\Domain\Password\Entity\PasswordResetToken;
use App\Domain\Password\Repository\PasswordResetTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class PasswordResetTokenParamConverter implements ParamConverterInterface
{
    private PasswordResetTokenRepository $tokenRepository;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
    public function supports(ParamConverter $configuration)
    {
        return PasswordResetToken::class === $configuration->getClass() && 'token' === $configuration->getName();
    }
}
