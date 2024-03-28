<?php

namespace App\Repository;

use App\Entity\PostHashtag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostHashtag>
 *
 * @method PostHashtag|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostHashtag|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostHashtag[]    findAll()
 * @method PostHashtag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostHashtagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostHashtag::class);
    }

//    /**
//     * @return PostHashtag[] Returns an array of PostHashtag objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PostHashtag
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
