<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{

    /**
     * @var BookingRepository
     */
    private $bookingRepository;

    /**
     * RoomRepository constructor.
     * @param BookingRepository $bookingRepository
     * @param ManagerRegistry $registry
     */
    public function __construct(BookingRepository $bookingRepository, ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
        $this->bookingRepository = $bookingRepository;
    }

    public function findAvailableRoom($start_date, $end_date){
        // get an ExpressionBuilder instance, so that you
        $expr = $this->_em->getExpressionBuilder();
        $sub = $this->bookingRepository->createQueryBuilder('b')
            ->select('b')
            ->where('b.start_date < :end_date AND b.end_date > :start_date')
            ->setParameter('start_date', $start_date)
            ->setParameter('end_date', $end_date);
        $sub = $sub->getQuery()->getResult();
        $notIn = array();
        foreach ($sub as $s) {
            array_push($notIn, $s->getRoom()->getId());
        }
        $qb = $this->createQueryBuilder('r')
            ->where($expr->notIn('r.id', $notIn ))
            ->getQuery()
            ->getResult();
        return $qb;
    }


    // /**
    //  * @return Room[] Returns an array of Room objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Room
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
