<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function findBestSeriesWithSpecificGenre(array $genres = []): array
    {
        $q = $this->createQueryBuilder('s')
            ->select('s.firstAirDate')
            ->addOrderBy('s.vote', 'DESC')
            ->addOrderBy('s.popularity', 'DESC')
            ->addOrderBy('s.name', 'ASC')
            ->andWhere('s.name like :terme or s.firstAirDate < :airDate')
            ->setParameter(':terme', '%non%')
            ->setParameter(':airDate', new \DateTime('2018-01-01'))
            ->andWhere('s.status = :status')
            ->setParameter(':status', 'returning');

        if (!empty($genres)) {
            $q->andWhere('s.genres in (:genres)')
                ->setParameter(':genres', $genres);
        }

        $expr = $q->expr();

        $cond1 = $expr->like('s.name', ':terme2');
        $cond2 = $expr->gte('s.firstAirDate', ':seuil');

        $q->andWhere($expr->orX($cond1, $cond2));
        $q->setParameter(':seuil', new \DateTime('2010-01-01'));
        $q->setParameter(':terme2', '%u%');

        return $q->getQuery()
            ->getResult();
    }

    public function getTheSerie(string $title): ?Serie
    {
        return $this->createQueryBuilder('s')
            ->where('s.name = :title')
            ->setParameter(':title', $title)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findSeriesWithSeasons(int $limit, int $offset): Paginator
    {
        $q = $this->createQueryBuilder('s')
            ->addSelect('seasons')
            ->leftJoin('s.seasons', 'seasons')
            ->orderBy('s.popularity', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($q);
    }



    public function getBestSeriesInDQL(): array
    {
        $dql = "SELECT s FROM App\Entity\Serie AS s 
            WHERE s.name LIKE :terme AND s.vote > 6 
            OR (s.popularity > 50 AND s.firstAirDate >= '2019-01-01')
            ORDER BY s.vote DESC, s.popularity DESC";

        return $this->getEntityManager()->createQuery($dql)
            ->setParameter('terme', '%non%')
            ->execute();

    }

    public function getBestSeriesInRawSQL(): array
    {
        $sql = "SELECT *, first_air_date as firstAirDate FROM serie as s WHERE s.first_air_date > :seuil ORDER BY s.popularity DESC";
        $conn = $this->getEntityManager()->getConnection();
        return $conn->prepare($sql)
            ->executeQuery(['seuil' => '2018-02-14'])
            ->fetchAllAssociative();
    }




    //    /**
    //     * @return Serie[] Returns an array of Serie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Serie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
