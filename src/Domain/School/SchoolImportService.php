<?php

namespace App\Domain\School;

use App\Domain\Coupon\Entity\Coupon;
use App\Domain\Coupon\Repository\CouponRepository;
use App\Domain\School\DTO\SchoolImportDTO;
use App\Domain\School\DTO\SchoolImportRow;
use App\Infrastructure\Mailing\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SchoolImportService
{

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly Mailer $mailer,
        private readonly CouponRepository $couponRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    /**
     * @return SchoolImportRow[]
     */
    public function process(SchoolImportDTO $data): array
    {
        $content = $data->file->getContent();
        $school = $data->school;
        /** @var SchoolImportRow[] $students */
        $students = $this->serializer->deserialize($content, SchoolImportRow::class . '[]', 'csv');
        $errors = $this->validator->validate($students, new Valid());

        // One or more lines of the CSV is not valid
        if (count($errors) > 0) {
            $firstError = $errors[0];
            throw new InvalidCSVException(sprintf("Erreur sur %s, %s", $firstError->getPropertyPath(), $firstError->getMessage()));
        }

        // Before adding students, check we didn't reach the limit of the school account
        $totalMonths = array_sum(array_map(fn (SchoolImportRow $row) => $row->months, $students));
        if ($totalMonths > $school->getCredits()) {
            throw new InvalidCSVException(sprintf("Vous essayez d'importer %s mois, mais il ne vous reste que %s de mois pour cette école", $totalMonths, $school->getCredits()));
        }


        /** @var Coupon[] $coupons */
        $coupons = [];
        foreach ($students as $student) {
            $coupons[] = $this->couponRepository->createForSchool(school: $school, prefix: $data->couponPrefix, email: $student->email, months: $student->months);
        }
        // Update school credits
        $school->setCredits($school->getCredits() - $totalMonths);
        $school->setEmailMessage($data->emailMessage);
        $school->setEmailSubject($data->emailSubject);
        $this->em->flush();

        // Send an email for each coupons
        foreach ($coupons as $coupon) {
            $email = $this->mailer->createEmail('mails/coupon/create.twig', [
                'email' => $coupon->getEmail(),
                'months' => $coupon->getMonths(),
                'message' => $data->emailMessage,
                'title' => $data->emailSubject,
                'code' => $coupon->getId()
            ])
                ->to($coupon->getEmail())
                ->subject($data->emailSubject);
            $this->mailer->send($email);
        }

        return $students;
    }
}
