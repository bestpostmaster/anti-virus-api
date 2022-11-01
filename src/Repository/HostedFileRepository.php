<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\HostedFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HostedFile>
 *
 * @method HostedFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method HostedFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method HostedFile[]    findAll()
 * @method HostedFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HostedFileRepository extends ServiceEntityRepository
{
    private ActionRequestedRepository $actionRequestedRepository;

    public function __construct(ManagerRegistry $registry, ActionRequestedRepository $actionRequestedRepository)
    {
        parent::__construct($registry, HostedFile::class);
        $this->actionRequestedRepository = $actionRequestedRepository;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(HostedFile $entity, bool $flush = true): void
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
    public function remove(HostedFile $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return HostedFile[]
     */
    public function searchBy(array $data, array $orderBy, int $limit, int $offset): array
    {
        $orderIndex = array_key_first($orderBy);
        $queryBuilder = $this->createQueryBuilder('f')
            ->andWhere('f.description LIKE :expression OR f.name LIKE :expression OR f.url LIKE :expression')
            ->setParameter('expression', '%'.$data['key'].'%')
            ->orderBy('f.'.$orderIndex, $orderBy[$orderIndex])
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        if (isset($data['userId'])) {
            $queryBuilder->andWhere('f.user = :userId')
             ->setParameter('userId', $data['userId']);
        }

        return $queryBuilder->getQuery()
                    ->getResult();
    }

    public function findRelatedActions(int $fileId): array
    {
        return $this->actionRequestedRepository->findRelatedActions($fileId);
    }
}
