<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trick>
 *
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function add(Trick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Trick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    private function getTrickQueryBuilder(): object
    {
        $queryBuilder = $this->createQueryBuilder('t');

        return $queryBuilder;
    }


    public function getTricks(int $page): object
    {
        $pageSize = 6;
        $firstResult = ($page - 1) * $pageSize;

        $queryBuilder = $this->getTrickQueryBuilder();

        // Set the returned page
        $queryBuilder->setFirstResult($firstResult);
        $queryBuilder->setMaxResults($pageSize);

        // Generate the Query
        $query = $queryBuilder->getQuery();
        $total = $this->getTotalTricks();

        //Generate the Paginator
        $paginator = new Paginator($query, true);
        return $paginator;
    }


    /**
     * getPaginatedTricks
     *
     * @param  mixed $page
     * @param  mixed $limit
     * @return void
     */
    public function getPaginatedTricks($page, $limit): array
    {
        $query = $this->createQueryBuilder('t')
            ->orderBy('t.createdAt')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }

    /**
     * Return number of tricks
     *
     * @return void
     */
    public function getTotalTricks(): int
    {
        $query = $this->createQueryBuilder('t')
            ->select('COUNT(t)');

        return $query->getQuery()->getSingleScalarResult();
    }
}
