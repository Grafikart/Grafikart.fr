<?php

namespace App\Domain\School;

use App\Domain\School\DTO\SchoolImportDTO;
use App\Domain\School\DTO\SchoolImportRow;
use App\Infrastructure\Mailing\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SchoolImportService
{

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly Mailer $mailer,
    ){

    }

    /**
     * @return SchoolImportRow[]
     */
    public function process(SchoolImportDTO $data): array
    {
        $content = $data->file->getContent();
        $school = $data->school;
        $school->setEmailTemplate($data->emailMessage);
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

        $school->setCredits($school->getCredits() - $totalMonths);

        // Send an email for each potential student
        foreach ($students as $student) {
            $email = $this->mailer->createEmail('mails/school/import.twig', [
                'email' => $student->email,
                'months' => $student->months,
                'message' => $data->emailMessage,
                'code' => 'GRAFI-0123-12312'
            ])
                ->to($student->email)
                ->subject('Compte premium Grafikart.fr');
            $this->mailer->send($email);
        }

        return $students;
    }

}
