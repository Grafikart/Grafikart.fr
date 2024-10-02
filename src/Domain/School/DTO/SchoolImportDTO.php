<?php

namespace App\Domain\School\DTO;

use App\Domain\School\Entity\School;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class SchoolImportDTO
{

    #[Assert\NotBlank()]
    #[Assert\File(extensions: ['csv' => ["text/csv", "application/csv", "text/x-comma-separated-values", "text/x-csv", "text/plain"]], maxSize: '2M')]
    public UploadedFile $file;

    #[Assert\NotBlank()]
    public string $emailMessage;

    #[Assert\NotBlank()]
    public string $emailSubject;

    #[Assert\NotBlank()]
    public string $couponPrefix;

    public function __construct(public readonly School $school)
    {
        $this->emailMessage = $this->school->getEmailMessage();
        $this->emailSubject = $this->school->getEmailSubject();
        $this->couponPrefix = $this->school->getCouponPrefix();
    }
}
