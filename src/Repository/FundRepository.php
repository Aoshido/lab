<?php

namespace App\Repository;

use App\Entity\Fund;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fund>
 *
 * @method Fund|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fund|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fund[]    findAll()
 * @method Fund[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fund::class);
    }

}
