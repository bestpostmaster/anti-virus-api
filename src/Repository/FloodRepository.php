<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Flood;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Flood>
 *
 * @method Flood|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flood|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flood[]    findAll()
 * @method Flood[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FloodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flood::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Flood $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Flood $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function removeOldFloods()
    {
        $timeLimit = 360;
        $currentTime = time();
        $queryBuilder = $this->createQueryBuilder('flood')
            ->delete()
            ->where("flood.lastTry + :timeLimit < :currentTime")
            ->setParameter('currentTime', $currentTime)
            ->setParameter('timeLimit', $timeLimit)
        ;

        return $queryBuilder->getQuery()->execute();
    }

    //removeOldFloods

    // /**
    //  * @return Flood[] Returns an array of Flood objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Flood
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
