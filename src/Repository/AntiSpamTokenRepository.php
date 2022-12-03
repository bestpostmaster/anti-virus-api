<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AntiSpamToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AntiSpamToken>
 *
 * @method AntiSpamToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AntiSpamToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AntiSpamToken[]    findAll()
 * @method AntiSpamToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AntiSpamTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AntiSpamToken::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(AntiSpamToken $entity, bool $flush = true): void
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
    public function remove(AntiSpamToken $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // removeOldAntiSpamTokens

    // /**
    //  * @return AntiSpamToken[] Returns an array of AntiSpamToken objects
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
    public function findOneBySomeField($value): ?AntiSpamToken
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
