<?php

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @return Review[] Returns a slice of reviews for a given album
     */
    public function findPaginatedByAlbum(Album $album, int $offset, int $limit): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.album = :album')
            ->setParameter('album', $album)
            ->orderBy('r.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countByAlbum(Album $album): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.album = :album')
            ->setParameter('album', $album)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
