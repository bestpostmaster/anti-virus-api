<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ActionRequested;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActionRequested>
 *
 * @method ActionRequested|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActionRequested|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActionRequested[]    findAll()
 * @method ActionRequested[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionRequestedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActionRequested::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ActionRequested $entity, bool $flush = true): void
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
    public function remove(ActionRequested $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findRelatedActions(int $fileId)
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->andWhere('f.hostedFileIds LIKE :expression')
            ->setParameter('expression', '%'.'"fileId":'.$fileId.'%')
            ->orderBy('f.id', 'ASC')
        ;

        return $queryBuilder->getQuery()
            ->getResult();
    }

    public function deleteRelatedActions(int $fileId)
    {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder->delete()
            ->andWhere('f.hostedFileIds LIKE :expression')
            ->setParameter('expression', '%'.'"fileId":'.$fileId.'%')
        ;

        return $queryBuilder->getQuery()
            ->getResult();
    }
}
