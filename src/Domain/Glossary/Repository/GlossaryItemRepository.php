<?php

namespace App\Domain\Glossary\Repository;

use App\Domain\Glossary\Entity\GlossaryItem;
use App\Domain\Glossary\Entity\GlossaryItemSimple;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<GlossaryItem>
 */
class GlossaryItemRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GlossaryItem::class);
    }

    /**
     * @return array<string, GlossaryItemSimple[]>
     */
    public function findWordsByLetters(): array
    {
        $dto = GlossaryItemSimple::class;
        /** @var GlossaryItemSimple[] $words */
        $words = $this->createQueryBuilder('g')
            ->select("NEW {$dto}(g.id, g.name, g.slug, IDENTITY(g.synonym))")
            ->orderBy('g.name')
            ->getQuery()
            ->getResult();
        /** @var GlossaryItemSimple[] $wordsById */
        $wordsById = collect($words)->keyBy('id')->toArray();

        // Rearrange les synonyms
        collect($words)
            ->filter(fn (GlossaryItemSimple $item) => $item->synonymId !== null)
            ->each(fn (GlossaryItemSimple $item) => $wordsById[$item->synonymId]->addSynonym($item));

        return collect($words)
            ->filter(fn (GlossaryItemSimple $item) => $item->synonymId === null)
            ->groupBy(fn (GlossaryItemSimple $item) => $item->name[0])
            ->toArray();
    }

//    /**
//     * @return GlossaryItem[] Returns an array of GlossaryItem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GlossaryItem
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
