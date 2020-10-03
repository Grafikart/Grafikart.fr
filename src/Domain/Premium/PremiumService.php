<?php

namespace App\Domain\Premium;

use App\Domain\Auth\AuthService;
use App\Domain\Premium\Entity\Plan;
use App\Domain\Premium\Entity\Transaction;
use App\Domain\Premium\Event\PremiumSubscriptionEvent;
use App\Infrastructure\Payment\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class PremiumService
{

    private AuthService $auth;
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(AuthService $auth, EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->auth = $auth;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function recordPayment(Payment $payment): void
    {
        /** @var Plan $plan */
        $plan = $this->em->getRepository(Plan::class)->find($payment->planId);
        $user = $this->auth->getUser();
        $transaction = (new Transaction())
            ->setPrice($payment->amount)
            ->setTax($payment->vat)
            ->setAuthor($user)
            ->setDuration($plan->getDuration())
            ->setMethod('paypal')
            ->setFirstname($payment->firstname)
            ->setLastname($payment->lastname)
            ->setCity($payment->city)
            ->setCountryCode($payment->countryCode)
            ->setAddress($payment->address)
            ->setPostalCode($payment->postalCode)
            ->setMethodRef($payment->id)
            ->setCreatedAt(new \DateTime());
        $this->em->persist($transaction);
        $now = new \DateTimeImmutable();
        $premiumEnd = $user->getPremiumEnd() ?: new \DateTimeImmutable();
        // Si l'utilisateur a déjà une date de fin de premium dans le futur, alors on incrémentera son compte
        $premiumEnd = $premiumEnd > $now ? $premiumEnd : new \DateTimeImmutable();
        $user->setPremiumEnd($premiumEnd->add(new \DateInterval("P{$plan->getDuration()}M")));
        $this->em->flush();
        $this->dispatcher->dispatch(new PremiumSubscriptionEvent($user));
    }
}
