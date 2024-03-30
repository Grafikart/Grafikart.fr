<?php

declare(strict_types=1);

namespace App\Http\Admin\Data;

use App\Domain\Coupon\Entity\Coupon;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property Coupon $entity
 */
class CouponCrudData extends AutomaticCrudData
{

    public string $id;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(1)]
    public int $months = 1;

    public function getId(): string
    {
        return $this->id;
    }

}
