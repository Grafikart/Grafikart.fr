<?php

namespace App\Command;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Infrastructure\Search\IndexerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class IndexCommand extends Command
{
    protected static $defaultName = 'app:index';
    private IndexerInterface $indexer;
    private NormalizerInterface $normalizer;
    private EntityManagerInterface $em;

    public function __construct(
        IndexerInterface $indexer,
        EntityManagerInterface $em,
        NormalizerInterface $normalizer
    ) {
        parent::__construct();
        $this->indexer = $indexer;
        $this->em = $em;
        $this->normalizer = $normalizer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $io->progressStart();
        $this->indexer->clean();

        // On importe les cours
        /** @var Course[] $courses */
        $courses = $this->em->getRepository(Course::class)->findAll();
        foreach ($courses as $course) {
            $io->progressAdvance();
            $this->indexer->index((array) $this->normalizer->normalize($course, 'search'));
        }
        $this->em->clear();

        // On importe les formations
        /** @var Formation[] $formations */
        $formations = $this->em->getRepository(Formation::class)->findAll();
        foreach ($formations as $formation) {
            $io->progressAdvance();
            $this->indexer->index((array) $this->normalizer->normalize($formation, 'search'));
        }
        $this->em->clear();

        $io->progressFinish();
        $io->success('Les contenus ont bien été indexés');

        return 0;
    }
}
