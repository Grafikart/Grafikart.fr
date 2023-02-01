<?php

namespace App\Command;

use App\Domain\Application\Entity\Content;
use App\Domain\Course\Entity\Chapter;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Cursus;
use App\Domain\Course\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method Application getApplication()
 */
#[AsCommand('app:seed')]
class SeedCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // On seed via hautelook
        $command = $this->getApplication()->find('hautelook:fixtures:load');
        $return = $command->run($input, $output);

        if (Command::SUCCESS !== $return) {
            return $return;
        }

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        // On crée des chapitres pour les formations
        $courses = $this->em->getRepository(Course::class)->findAll();
        $chunks = array_chunk($courses, 3);
        $formations = $this->em->getRepository(Formation::class)->findAll();
        foreach ($formations as $k => $formation) {
            $chapters = [];
            for ($i = 1; $i < 3; ++$i) {
                /** @var Content[] $chunk */
                $chunk = array_pop($chunks);
                $chapters[] = (new Chapter())
                    ->setTitle("Chapitre {$i}")
                    ->setModules($chunk);
                /** @var Course $course */
                foreach ($chunk as $course) {
                    $course->setFormation($formation);
                }
            }
            $formation->setChapters($chapters);
        }
        $this->em->flush();

        // On crée des chapitres pour les cursus
        $cursus = $this->em->getRepository(Cursus::class)->findAll();
        /** @var Content[] $items */
        $items = array_values([...$courses, ...$formations]);
        foreach ($cursus as $c) {
            $chapters = [];
            for ($i = 1; $i < 3; ++$i) {
                /** @var int[] $keys */
                $keys = array_rand($items, random_int(2, 6));
                $modules = array_map(fn (int $k) => $items[$k], $keys);
                $chapters[] = (new Chapter())
                    ->setTitle("Chapitre {$i}")
                    ->setModules($modules);
                /** @var callable $callable */
                $callable = $c->addModule(...);
                array_map($callable, $modules);
            }
            $c->setChapters($chapters);
        }
        $this->em->flush();

        return Command::SUCCESS;
    }
}
