<?php

declare(strict_types=1);

namespace App\Domain\Premium\Subscriber;

use App\Domain\Premium\Entity\Plan;
use App\Domain\Premium\Entity\Transaction;
use App\Domain\Premium\Event\PremiumSubscriptionEvent;
use App\Domain\Premium\Exception\PaymentPlanMissMatchException;
use App\Infrastructure\Payment\Event\PaymentEvent;
use App\Infrastructure\Payment\Stripe\StripePayment;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PaymentEvent::class => 'onPayment',
        ];
    }

    public function onPayment(PaymentEvent $event): void
    {
        // On regarde si le paiement correspond à un plan
        $payment = $event->getPayment();
        $user = $event->getUser();
        $plan = $this->em->getRepository(Plan::class)->findOneBy(['price' => $payment->amount]);
        if (null === $plan) {
            throw new PaymentPlanMissMatchException();
        }
        $type = 'paypal';
        if ($payment instanceof StripePayment) {
            $type = 'stripe';
        }

        // On enregistre la transaction
        $transaction = (new Transaction())
            ->setPrice($payment->amount)
            ->setTax($payment->vat)
            ->setAuthor($event->getUser())
            ->setDuration($plan->getDuration())
            ->setMethod($type)
            ->setFirstname($payment->firstname)
            ->setLastname($payment->lastname)
            ->setCity($payment->city)
            ->setCountryCode($payment->countryCode)
            ->setAddress($payment->address)
            ->setPostalCode($payment->postalCode)
            ->setMethodRef($payment->id)
            ->setFee($payment->fee)
            ->setCreatedAt(new \DateTime());
        $this->em->persist($transaction);

        // On met à jour la date de fin de premium de l'utilisateur
        $now = new \DateTimeImmutable();
        $premiumEnd = $user->getPremiumEnd() ?: new \DateTimeImmutable();
        // Si l'utilisateur a déjà une date de fin de premium dans le futur, alors on incrémentera son compte
        $premiumEnd = $premiumEnd > $now ? $premiumEnd : new \DateTimeImmutable();
        $user->setPremiumEnd($premiumEnd->add(new \DateInterval("P{$plan->getDuration()}M")));

        // Flush & dispatch
        $this->em->flush();
        $this->dispatcher->dispatch(new PremiumSubscriptionEvent($user));
    }
}
