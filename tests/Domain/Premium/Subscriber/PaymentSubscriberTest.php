<?php

namespace App\Tests\Domain\Premium\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Premium\Entity\Plan;
use App\Domain\Premium\Entity\Transaction;
use App\Domain\Premium\Event\PremiumSubscriptionEvent;
use App\Domain\Premium\Exception\PaymentPlanMissMatchException;
use App\Domain\Premium\Repository\PlanRepository;
use App\Domain\Premium\Subscriber\PaymentSubscriber;
use App\Infrastructure\Payment\Event\PaymentEvent;
use App\Infrastructure\Payment\Payment;
use App\Tests\EventSubscriberTest;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;

class PaymentSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(PaymentSubscriber::class, PaymentEvent::class);
    }

    private function getSubscriber(): array
    {
        $plan = (new Plan())
            ->setPrice(100)
            ->setDuration(12);
        $em = $this->createMock(EntityManagerInterface::class);
        $planRepository = $this->createMock(PlanRepository::class);
        $planRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturnCallback(fn ($params) => $params['price'] === $plan->getPrice() ? $plan : null);
        $em->expects($this->once())->method('getRepository')->with(Plan::class)->willReturn($planRepository);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->any())->method('dispatch');

        $subscriber = new PaymentSubscriber($em, $dispatcher);

        return [$subscriber, $em, $dispatcher];
    }

    private function getEvent(): PaymentEvent
    {
        $payment = new Payment();
        $payment->id = 'platform_id';
        $payment->amount = 100;
        $user = new User();

        return new PaymentEvent($payment, $user);
    }

    public function testThrowExceptionIfNoPlanIsFound()
    {
        [$subscriber] = $this->getSubscriber();
        $event = $this->getEvent();
        $event->getPayment()->amount = 999;
        $this->expectException(PaymentPlanMissMatchException::class);
        $this->dispatch($subscriber, $event);
    }

    public function testPersistTransaction()
    {
        /** @var MockObject $em */
        [$subscriber, $em] = $this->getSubscriber();
        $em->expects($this->once())->method('persist')->with($this->callback(function (Transaction $transaction) {
            return 100 == $transaction->getPrice();
        }));
        $em->expects($this->once())->method('flush');
        $this->dispatch($subscriber, $this->getEvent());
    }

    public function testDispatchPremiumEvent()
    {
        /** @var MockObject $dispatcher */
        [$subscriber, $em, $dispatcher] = $this->getSubscriber();
        $dispatcher->expects($this->once())->method('dispatch')->with($this->isInstanceOf(PremiumSubscriptionEvent::class));
        $this->dispatch($subscriber, $this->getEvent());
    }

    public function testPushPremiumEndTime()
    {
        [$subscriber] = $this->getSubscriber();
        $event = $this->getEvent();
        $user = $event->getUser();
        $this->dispatch($subscriber, $event);
        $this->assertGreaterThan((new \DateTimeImmutable('+ 10 months'))->getTimestamp(), $user->getPremiumEnd()->getTimestamp());
    }

    public function testPushPremiumEndTimeFurther()
    {
        [$subscriber] = $this->getSubscriber();
        $event = $this->getEvent();
        $user = $event->getUser();
        $user->setPremiumEnd(new \DateTimeImmutable('+ 10 months'));
        $this->dispatch($subscriber, $event);
        $this->assertGreaterThan((new \DateTimeImmutable('+ 20 months'))->getTimestamp(), $user->getPremiumEnd()->getTimestamp());
    }
}
