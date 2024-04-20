<?php

namespace App\Domain\School;

use App\Domain\Coupon\Entity\Coupon;
use App\Domain\Coupon\Repository\CouponRepository;
use App\Domain\School\DTO\SchoolImportDTO;
use App\Domain\School\DTO\SchoolImportRow;
use App\Domain\School\DTO\SchoolPreprocessResult;
use App\Domain\School\Entity\School;
use App\Infrastructure\Mailing\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SchoolImportService
{

    public function __construct(
        private readonly SerializerInterface    $serializer,
        private readonly ValidatorInterface     $validator,
        private readonly Mailer                 $mailer,
        private readonly CouponRepository       $couponRepository,
        private readonly EntityManagerInterface $em
    )
    {
    }

    /**
     * Vérifie la structure des données et renvois le contenu du CSV (sous forme d'objet)
     */
    public function preprocess(SchoolImportDTO $data): SchoolPreprocessResult
    {
        $school = $data->school;
        $result = $this->deserializeContent($data->file->getContent(), $school);
        $school->setEmailMessage($data->emailMessage);
        $school->setEmailSubject($data->emailSubject);
        $this->em->flush();

        return $result;
    }

    /**
     * Génère les coupons et envoie les emails aux étudiants
     */
    public function process(string $csvContent, School $school): SchoolPreprocessResult
    {
        $result = $this->deserializeContent($csvContent, $school);
        /** @var Coupon[] $coupons */
        $coupons = [];
        foreach ($result->rows as $student) {
            $coupons[] = $this->couponRepository->createForSchool(school: $school, prefix: $school->getCouponPrefix(), email: $student->email, months: $student->months);
        }

        // Update school credits
        $totalMonths = $result->getMonths();
        $school->setCredits($school->getCredits() - $totalMonths);
        $this->em->flush();

        // Send an email for each coupons
        foreach ($coupons as $coupon) {
            $email = $this->mailer->createEmail('mails/coupon/create.twig', [
                'email' => $coupon->getEmail(),
                'months' => $coupon->getMonths(),
                'message' => $school->getEmailMessage(),
                'title' => $school->getEmailSubject(),
                'code' => $coupon->getId()
            ])
                ->to($coupon->getEmail())
                ->subject($school->getEmailSubject());
            $this->mailer->send($email);
        }
        return $result;
    }

    /**
     * @param string $content
     * @return SchoolPreprocessResult
     */
    private function deserializeContent(string $content, School $school): SchoolPreprocessResult
    {
        /** @var SchoolImportRow[] $students */
        $students = $this->serializer->deserialize($content, SchoolImportRow::class . '[]', 'csv');
        $errors = $this->validator->validate($students, new Valid());
        $result = new SchoolPreprocessResult(
            rows: $students,
            csv: $content,
            school: $school
        );

        // One or more lines of the CSV is not valid
        if (count($errors) > 0) {
            $firstError = $errors[0];
            throw new InvalidCSVException(sprintf("Erreur sur %s, %s", $firstError->getPropertyPath(), $firstError->getMessage()));
        }

        // Before adding students, check we didn't reach the limit of the school account
        $totalMonths = $result->getMonths();
        if ($totalMonths > $school->getCredits()) {
            throw new InvalidCSVException(sprintf("Vous essayez d'importer %s mois, mais il ne vous reste que %s de mois pour cette école", $totalMonths, $school->getCredits()));
        }

        return $result;
    }
}
