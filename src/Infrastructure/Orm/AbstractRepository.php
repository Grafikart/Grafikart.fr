<?php

namespace App\Infrastructure\Orm;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template E of object
 *
 * @method E|null find($id, $lockMode = null, $lockVersion = null)
 * @method E|null findOneBy(array $criteria, array $orderBy = null)
 * @method E[]    findAll()
 * @method E[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @param class-string<E> $entityClass
     *
     * @psalm-param class-string<E> $entityClass
     */
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * Trouve une entité par sa clef primaire et renvoie une exception en cas d'absence.
     *
     * @return E
     */
    public function findOrFail(int|string $id): object
    {
        $entity = $this->find($id, null, null);
        if (null === $entity) {
            throw EntityNotFoundException::fromClassNameAndIdentifier($this->getEntityName(), [(string) $id]);
        }

        return $entity;
    }

    public function findByCaseInsensitive(array $conditions): array
    {
        return $this->findByCaseInsensitiveQuery($conditions)->getResult();
    }

    /**
     * @return E|null
     */
    public function findOneByCaseInsensitive(array $conditions): ?object
    {
        return $this->findByCaseInsensitiveQuery($conditions)->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * Crée une requête qui peut être iterable, mais qui ne récupère les données que lors de la première itération.
     *
     * @param string $indexBy the index for the from
     *
     * @return IterableQueryBuilder<E>
     */
    public function createIterableQuery(string $alias, $indexBy = null): IterableQueryBuilder
    {
        /** @var IterableQueryBuilder<E> $queryBuilder */
        $queryBuilder = new IterableQueryBuilder($this->getEntityManager());

        return $queryBuilder->select($alias)->from($this->getEntityName(), $alias, $indexBy);
    }

    /**
     * @template T of array
     *
     * @param T $items
     *
     * @return T
     * */
    public function hydrateRelation(array $items, string $propertyName): array
    {
        if (!isset($items[0])) {
            return $items;
        }

        $getter = 'get'.ucfirst($propertyName);
        $setter = 'set'.ucfirst($propertyName);
        $ids = array_map(fn ($item) => $item->$getter()->getId(), $items);
        /** @var class-string $entityClass */
        $entityClass = $items[0]::class;
        $reflection = new \ReflectionClass($entityClass);
        $relationType = $reflection->getProperty($propertyName)->getType();
        if (!$relationType instanceof \ReflectionNamedType) {
            throw new \Exception(sprintf("Impossible d'ingérer le type de la propriété %s de %s", $propertyName, $entityClass));
        }
        /** @var class-string $relationClass */
        $relationClass = $relationType->getName();
        if (!$relationClass || !str_contains($relationClass, 'Entity')) {
            throw new \Exception(sprintf("Impossible d'hydrater la relation dans un %s, la propriété %s (%s) n'est pas une entité", $entityClass, $propertyName, $relationClass));
        }

        // Trouve les éléments liés
        /** @var object[] $relationItems */
        $relationItems = $this->getEntityManager()->getRepository($relationClass)->findBy(['id' => $ids]);
        // @phpstan-ignore argument.templateType
        $relationItemsById = collect($relationItems)->keyBy(fn (object $item) => method_exists($item, 'getId') ? $item->getId() : -1)->toArray();

        // Remplit la relation
        foreach ($items as $item) {
            $item->$setter($relationItemsById[$item->$getter()->getId()] ?? null);
        }

        return $items;
    }

    private function findByCaseInsensitiveQuery(array $conditions): Query
    {
        $conditionString = [];
        $parameters = [];
        foreach ($conditions as $k => $v) {
            $conditionString[] = "LOWER(o.$k) = :$k";
            $parameters[] = new Query\Parameter($k, strtolower((string) $v));
        }

        return $this->createQueryBuilder('o')
            ->where(join(' AND ', $conditionString))
            ->setParameters(new ArrayCollection($parameters))
            ->getQuery();
    }
}
