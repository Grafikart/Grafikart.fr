<?php

namespace App\Http\Admin\Data;

use App\Infrastructure\Payment\Validator\StripePlan;
use Symfony\Component\Validator\Constraints as Assert;

class CrudPlanData extends AutomaticCrudData
{

    /**
     * @Assert\NotBlank()
     */
    public string $name = '';

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value="1")
     */
    public float $price = 0;

    /**
     * @Assert\Positive()
     */
    public int $duration = 1;

    /**
     * @Assert\NotBlank()
     * @StripePlan()
     */
    public ?string $stripeId = null;

}
