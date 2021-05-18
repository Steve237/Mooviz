<?php

namespace App\Repository;

use App\Entity\Videobackground;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Videobackground|null find($id, $lockMode = null, $lockVersion = null)
 * @method Videobackground|null findOneBy(array $criteria, array $orderBy = null)
 * @method Videobackground[]    findAll()
 * @method Videobackground[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideobackgroundRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Videobackground::class);
    }

   
}
