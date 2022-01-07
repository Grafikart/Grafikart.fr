<?php

namespace App\Command;

use App\Domain\Blog\Post;
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

    public function __construct(
        private IndexerInterface $indexer,
        private EntityManagerInterface $em,
        private NormalizerInterface $normalizer
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $io->progressStart();
        $this->indexer->clean();

        // On importe les cours
        $types = [Course::class, Formation::class, Post::class];
        foreach ($types as $type) {
            $items = $this->em->getRepository($type)->findBy(['online' => true]);
            foreach ($items as $item) {
                $io->progressAdvance();
                $this->indexer->index((array) $this->normalizer->normalize($item, 'search'));
            }
            $this->em->clear();
        }

        $io->progressFinish();
        $io->success('Les contenus ont bien été indexés');

        return 0;
    }
}
