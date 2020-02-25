<?php

namespace App\Infrastructure\Importer;

use App\Domain\Attachment\Attachment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DataImporter
{

    use DatabaseImporterTools;

    private CoursesImporter $coursesImporter;
    private FormationImporter $formationImporter;
    private UserImporter $userImporter;
    private BlogImporter $blogImporter;
    private EntityManagerInterface $em;
    private CommentImporter $commentImporter;

    public function __construct(
        CoursesImporter $coursesImporter,
        FormationImporter $formationImporter,
        UserImporter $userImporter,
        BlogImporter $blogImporter,
        CommentImporter $commentImporter,
        EntityManagerInterface $em
    )
    {
        $this->coursesImporter = $coursesImporter;
        $this->formationImporter = $formationImporter;
        $this->userImporter = $userImporter;
        $this->blogImporter = $blogImporter;
        $this->em = $em;
        $this->commentImporter = $commentImporter;
    }

    public function importBlog(SymfonyStyle $io): void
    {
        $this->blogImporter->import($io);
    }

    public function importUser(SymfonyStyle $io): void
    {
        $this->userImporter->import($io);
    }

    public function importCourse(SymfonyStyle $io): void
    {
        $this->coursesImporter->import($io);
    }

    public function importFormation(SymfonyStyle $io): void
    {
        $this->formationImporter->import($io);
    }

    public function importComment(SymfonyStyle $io): void
    {
        $this->commentImporter->import($io);
    }

    public function resetContent(SymfonyStyle $io): void
    {
        $this->truncate('content');
        $this->truncate($this->em->getClassMetadata(Attachment::class)->getTableName());
    }

}
