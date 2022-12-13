<?php

namespace App\Repository;

use App\Entity\Trick;
use App\Entity\UserMessage;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<UserMessage>
 *
 * @method UserMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMessage[]    findAll()
 * @method UserMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMessage::class);
    }

    public function add(UserMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * getPaginatedComments
     *
     * @param  mixed $page
     * @param  mixed $limit
     * @return void
     */
    public function getPaginatedComments($page, $limit, Trick $trick): array
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt')
            ->where('c.status = 1')
            ->andWhere('c.trick = :trick')
            ->setParameter('trick', $trick->getId())
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }

    /**
     * Return all comments order by created at DESC
     *
     * @return array
     */
    public function findAllOrderByCreatedAt(int $page, int $limit): array
    {
        $query = $this->createQueryBuilder('c')
        ->orderBy('c.createdAt', 'DESC')
        ->setFirstResult(($page * $limit) - $limit)
        ->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }

    /**
     * Return number of comments
     *
     * @return void
     */
    public function getTotalComments(): int
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c)');

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * get all modarated comments of a trick
     *
     * @param integer $page
     * @param integer $trickId
     * @return object
     */
    public function getCommentsOfATrick(int $page, int $trickId): object
    {
        $pageSize = 10;
        $firstResult = (($page - 1) * $pageSize);

        $queryBuilder = $this->createQueryBuilder('c')
        ->andWhere('c.trick = :trickId')
        ->andWhere('c.status = 1')
        ->setParameter('trickId', $trickId);

        // Set the returned page
        $queryBuilder->setFirstResult($firstResult);
        $queryBuilder->setMaxResults($pageSize);

        // Generate the Query
        $query = $queryBuilder->getQuery();

        //Generate the Paginator
        $paginator = new Paginator($query, true);
        return $paginator;
    }

    /**
     * get total comments of a trick
     *
     * @param integer $trickId
     * @return array
     */
    public function getTotalCommentOfATrick(int $trickId): array
    {
        $query = $this->createQueryBuilder('c')
        ->andWhere('c.trick = :trickId')
        ->setParameter('trickId', $trickId);

        return $query->getQuery()->getResult();
    }
}
