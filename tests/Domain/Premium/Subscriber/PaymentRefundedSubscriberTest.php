<?php

namespace App\Tests\Domain\Premium\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Premium\Entity\Transaction;
use App\Domain\Premium\Event\PremiumCancelledEvent;
use App\Domain\Premium\Repository\TransactionRepository;
use App\Domain\Premium\Subscriber\PaymentRefundedSubscriber;
use App\Infrastructure\Payment\Event\PaymentRefundedEvent;
use App\Infrastructure\Payment\Payment;
use App\Tests\EventSubscriberTest;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;

class PaymentRefundedSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(PaymentRefundedSubscriber::class, PaymentRefundedEvent::class);
    }

    private function getSubscriber($transaction)
    {
        $transactionRepository = $this->createMock(TransactionRepository::class);
        $transactionRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($transaction);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $subscriber = new PaymentRefundedSubscriber($transactionRepository, $dispatcher, $em);

        return [$subscriber, $dispatcher, $em];
    }

    public function testDoesNothingIfPaymentAlreadyRefunded()
    {
        $user = new User();
        $initialPremiumEnd = new \DateTimeImmutable('+ 3 months');
        $user->setPremiumEnd($initialPremiumEnd);
        $transaction = (new Transaction())
            ->setDuration(1)
            ->setRefunded(true)
            ->setAuthor($user);
        $payment = new Payment();
        $payment->id = 'platform_id';

        [$subscriber] = $this->getSubscriber($transaction);
        $event = new PaymentRefundedEvent($payment);
        $this->dispatch($subscriber, $event);
        $this->assertEquals($initialPremiumEnd->getTimestamp(), $user->getPremiumEnd()->getTimestamp());
    }

    public function testDispatchPremiumCancelled()
    {
        $transaction = (new Transaction())
            ->setDuration(1)
            ->setAuthor(new User());
        $payment = new Payment();
        $payment->id = 'platform_id';

        [$subscriber, $dispatcher] = $this->getSubscriber($transaction);
        $dispatcher->expects($this->once())->method('dispatch')->with($this->callback(fn (PremiumCancelledEvent $event) => $event->getUser() === $transaction->getAuthor()));
        $event = new PaymentRefundedEvent($payment);
        $this->dispatch($subscriber, $event);
    }

    public function testDowngradeUser()
    {
        $user = new User();
        $initialPremiumEnd = new \DateTimeImmutable('+ 3 months');
        $user->setPremiumEnd($initialPremiumEnd);
        $transaction = (new Transaction())
            ->setDuration(1)
            ->setAuthor($user);
        $payment = new Payment();
        $payment->id = 'platform_id';

        [$subscriber] = $this->getSubscriber($transaction);
        $event = new PaymentRefundedEvent($payment);
        $this->dispatch($subscriber, $event);
        $this->assertLessThan($initialPremiumEnd->getTimestamp(), $user->getPremiumEnd()->getTimestamp());
    }

    public function testMarkTransactionAsReimbursed()
    {
        $user = new User();
        $initialPremiumEnd = new \DateTimeImmutable('+ 3 months');
        $user->setPremiumEnd($initialPremiumEnd);
        $transaction = (new Transaction())
            ->setDuration(1)
            ->setAuthor($user);
        $this->assertFalse($transaction->isRefunded());
        $payment = new Payment();
        $payment->id = 'platform_id';

        /** @var MockObject $em */
        [$subscriber, $dispatcher, $em] = $this->getSubscriber($transaction);
        $em->expects($this->once())->method('flush');
        $event = new PaymentRefundedEvent($payment);
        $this->dispatch($subscriber, $event);
        $this->assertTrue($transaction->isRefunded());
    }
}
