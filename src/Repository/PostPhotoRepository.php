<?php

namespace App\Repository;

use App\Entity\PostPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostPhoto>
 *
 * @method PostPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostPhoto[]    findAll()
 * @method PostPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostPhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostPhoto::class);
    }

//    /**
//     * @return PostPhoto[] Returns an array of PostPhoto objects
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

//    public function findOneBySomeField($value): ?PostPhoto
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
