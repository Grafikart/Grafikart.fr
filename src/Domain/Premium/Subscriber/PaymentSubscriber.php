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
    public function __construct(private readonly EntityManagerInterface $em, private readonly EventDispatcherInterface $dispatcher)
    {
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
            ->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($transaction);

        $user->addPremiumMonths($plan->getDuration());

        // Flush & dispatch
        $this->em->flush();
        $this->dispatcher->dispatch(new PremiumSubscriptionEvent($user));
    }
}
