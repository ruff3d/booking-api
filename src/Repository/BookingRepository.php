<?php

namespace App\Repository;

use App\Entity\Booking;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param int $limit
     *
     * @return Booking[] Returns an array of Booking objects
     */
    public function findByInterval(DateTimeInterface $from, DateTimeInterface $to, int $limit = 10): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.bookedFrom >= :from AND b.bookedTo <= :to')
            ->setParameters(['from' => $from, 'to' => $to])
            ->orderBy('b.id', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Booking[] $bookings
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveMany(array $bookings): void
    {
        foreach ($bookings as $booking) {
            $this->_em->persist($booking);
        }

        $this->_em->flush();
        $this->_em->clear();
    }
}
