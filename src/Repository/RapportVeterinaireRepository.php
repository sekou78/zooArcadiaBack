<?php

namespace App\Repository;

use App\Entity\RapportVeterinaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RapportVeterinaire>
 */
class RapportVeterinaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RapportVeterinaire::class);
    }

    public function findByCriteria(array $criteria)
    {
        $qb = $this->createQueryBuilder('r');
        if (isset($criteria['animal'])) {
            $qb->andWhere('r.animal = :animal')->setParameter('animal', $criteria['animal']);
        }
        if (isset($criteria['date'])) {
            $qb->andWhere('r.date = :date')->setParameter('date', $criteria['date']);
        }
        return $qb->getQuery()->getResult();
    }


    //    /**
    //     * @return RapportVeterinaire[] Returns an array of RapportVeterinaire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RapportVeterinaire
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
