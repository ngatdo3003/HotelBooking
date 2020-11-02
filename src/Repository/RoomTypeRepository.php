<?php

namespace App\Repository;

use App\Entity\RoomType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RoomType|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomType|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomType[]    findAll()
 * @method RoomType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomTypeRepository extends ServiceEntityRepository
{
    private $priceRepository;

    /**
     * RoomTypeRepository constructor.
     * @param ManagerRegistry $registry
     * @param PriceRepository $priceRepository
     */
    public function __construct(ManagerRegistry $registry, PriceRepository $priceRepository)
    {
        parent::__construct($registry, RoomType::class);
        $this->priceRepository = $priceRepository;
    }

    public function caculatePrice($room_type_id, $start_date, $end_date){
        $prices = $this->priceRepository->createQueryBuilder('p')
            ->andWhere('p.room_type = :room_type_id')
            ->andWhere('p.date < :end_date AND p.date >= :start_date')
            ->setParameter('start_date', $start_date)
            ->setParameter('end_date', $end_date)
            ->setParameter('room_type_id', $room_type_id)
            ->select('SUM(p.price) as sum, COUNT(p.id) as cnt')
            ->getQuery()
            ->getResult();
        $duration = date_diff(new \DateTime($end_date), new \DateTime($start_date));
        $room_type = $this->find($room_type_id);
        return ($duration->days - array_sum(array_column($prices, "cnt")))*$room_type->getPrice() + array_sum(array_column($prices, "sum"));
    }
    // /**
    //  * @return RoomType[] Returns an array of RoomType objects
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
    public function findOneBySomeField($value): ?RoomType
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
