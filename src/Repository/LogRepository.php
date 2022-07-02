<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Log>
 *
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function add(Log $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Log $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * $return integer Return count based on parameters
     */
    public function queryCount($serviceNames,$startDate = null, $endDate = null, $statusCode = null){
        $queryBuilder = $this->createQueryBuilder('l')->select('COUNT(l.id)');
        if($serviceNames){
            $queryBuilder->where('l.serviceName in (:service_names)')
            ->setParameter('service_names',$serviceNames);
        }

        if($startDate && $endDate){
            $queryBuilder->andWhere("l.date BETWEEN :start AND :end")
            ->setParameter("start", $startDate)
            ->setParameter("end", $endDate);
        }else if($startDate){
            $queryBuilder->andWhere("l.date > :start")
            ->setParameter("start", $startDate);
        }else if($endDate){
            $queryBuilder->andWhere("l.date < :end")
            ->setParameter("end", $endDate);
        }

        if($statusCode){
            $queryBuilder->andWhere("l.statusCode = :code")
            ->setParameter("code", $statusCode);
        }

        $count = $queryBuilder->getQuery()->getSingleScalarResult();
        return $count;        
    }
//    /**
//     * @return Log[] Returns an array of Log objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Log
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
