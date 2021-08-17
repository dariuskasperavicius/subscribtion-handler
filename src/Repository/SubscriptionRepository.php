<?php

namespace App\Repository;

use App\Entity\Subscribtion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subscribtion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscribtion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscribtion[]    findAll()
 * @method Subscribtion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscribtion::class);
    }

    public function save(Subscribtion $transaction): void
    {
        $this->getEntityManager()->persist($transaction);
        $this->getEntityManager()->flush();
    }
}
