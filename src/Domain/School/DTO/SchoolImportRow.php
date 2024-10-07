<?php

namespace App\Domain\School\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SchoolImportRow
{
    #[Assert\NotBlank()]
    #[Assert\Email()]
    public string $email;

    #[Assert\NotBlank()]
    #[Assert\GreaterThan(0)]
    #[Assert\LessThan(24)]
    public int $months;
}
