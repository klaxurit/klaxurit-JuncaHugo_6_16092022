<?php

namespace App\Repository;

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

    private function getCommentQueryBuilder(){
        $queryBuilder = $this->createQueryBuilder('c');

        return $queryBuilder;
    }

    public function getComments($page){
        $pageSize = 2;
		$firstResult = ($page - 1) * $pageSize;

		$queryBuilder = $this->getCommentQueryBuilder();
		
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
     * getPaginatedComments
     *
     * @param  mixed $page
     * @param  mixed $limit
     * @return void
     */
    public function getPaginatedComments($page, $limit)
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);

            return $query->getQuery()->getResult();
    }

    /**
     * Return number of comments
     *
     * @return void
     */
    public function getTotalComments()
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c)');

        return $query->getQuery()->getSingleScalarResult();
    }

//    /**
//     * @return UserMessage[] Returns an array of UserMessage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserMessage
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
