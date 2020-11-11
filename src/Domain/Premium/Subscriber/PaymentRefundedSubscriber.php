<?php

declare(strict_types=1);

namespace App\Domain\Premium\Subscriber;

use App\Domain\Premium\Event\PremiumCancelledEvent;
use App\Domain\Premium\Repository\TransactionRepository;
use App\Infrastructure\Payment\Event\PaymentRefundedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentRefundedSubscriber implements EventSubscriberInterface
{
    private TransactionRepository $transactionRepository;
    private EventDispatcherInterface $dispatcher;

    private EntityManagerInterface $em;

    public function __construct(TransactionRepository $transactionRepository, EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {
        $this->transactionRepository = $transactionRepository;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PaymentRefundedEvent::class => 'onPaymentReimbursed',
        ];
    }

    public function onPaymentReimbursed(PaymentRefundedEvent $event): void
    {
        $transaction = $this->transactionRepository->findOneBy(['methodRef' => $event->getPayment()->id]);
        if (null === $transaction || $transaction->isRefunded()) {
            return;
        }

        // On réduit la durée d'abonnement de l'utilisateur
        $user = $transaction->getAuthor();
        $premiumEnd = $user->getPremiumEnd();
        if (null !== $premiumEnd) {
            $user->setPremiumEnd($premiumEnd->sub(new \DateInterval("P{$transaction->getDuration()}M")));
        }

        $transaction->setRefunded(true);
        $this->em->flush();

        $this->dispatcher->dispatch(new PremiumCancelledEvent($transaction->getAuthor()));
    }
}
