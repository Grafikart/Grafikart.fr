<?php

namespace App\Domain\Coupon;

use App\Domain\Coupon\DTO\CouponClaimDTO;
use App\Domain\Coupon\Entity\Coupon;
use App\Domain\Coupon\Event\CouponClaimedEvent;
use App\Domain\Coupon\Repository\CouponRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class CouponClaimerService
{

    public function __construct(
        private readonly CouponRepository         $couponRepository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function claim(CouponClaimDTO $claim): Coupon
    {
        $user = $claim->getUser();
        $coupon = $this->couponRepository->findOrFail($claim->getCode());
        $school = $coupon->getSchool();
        $coupon
            ->setClaimedAt(new \DateTimeImmutable())
            ->setClaimedBy($user);
        if ($school) {
            $school->addStudent($user);
        }
        $user->addPremiumMonths($coupon->getMonths());
        $this->dispatcher->dispatch(new CouponClaimedEvent($coupon));
        return $coupon;
    }
}
