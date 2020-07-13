<?php

namespace App\Command;

use App\Domain\Course\Repository\CourseRepository;
use App\Domain\Course\Repository\FormationRepository;
use App\Domain\Forum\Repository\TopicRepository;
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
    private FormationRepository $formationRepository;
    private CourseRepository $courseRepository;
    private NormalizerInterface $normalizer;
    private TopicRepository $topicRepository;
    private EntityManagerInterface $em;

    public function __construct(
        IndexerInterface $indexer,
        FormationRepository $formationRepository,
        TopicRepository $topicRepository,
        CourseRepository $courseRepository,
        EntityManagerInterface $em,
        NormalizerInterface $normalizer)
    {
        parent::__construct();
        $this->indexer = $indexer;
        $this->formationRepository = $formationRepository;
        $this->courseRepository = $courseRepository;
        $this->normalizer = $normalizer;
        $this->topicRepository = $topicRepository;
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $io->progressStart();

        $courses = $this->courseRepository->findAll();
        foreach ($courses as $course) {
            $io->progressAdvance();
            $this->indexer->index((array) $this->normalizer->normalize($course, 'search'));
        }

        $formations = $this->formationRepository->findAll();
        foreach ($formations as $formation) {
            $io->progressAdvance();
            $this->indexer->index((array) $this->normalizer->normalize($formation, 'search'));
        }

        $topics = $this->topicRepository->findAllBatched();
        foreach ($topics as $topic) {
            $io->progressAdvance();
            $this->indexer->index((array) $this->normalizer->normalize($topic, 'search'));
        }

        $io->progressFinish();
        $io->success('ça marche');

        return 0;
    }
}
