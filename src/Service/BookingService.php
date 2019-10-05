<?php

namespace App\Service;

use App\Repository\BookingRepository;
use DateTimeInterface;
use Doctrine\ORM\{NonUniqueResultException, NoResultException, OptimisticLockException, ORMException};

class BookingService
{
    /**
     * @var BookingRepository
     */
    private $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * @return array
     */
    public function getAllReservations(): array
    {
        return $this->bookingRepository->findAll();
    }

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return array
     */
    public function findByInterval(DateTimeInterface $from, DateTimeInterface $to): array
    {
        return $this->bookingRepository->findByInterval($from, $to);
    }

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function isAvailable(DateTimeInterface $from, DateTimeInterface $to): bool
    {
        return $this->bookingRepository->checkAvailability($from, $to);

    }

    /**
     * @param array $bookings
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveMany(array $bookings): void
    {
        $this->bookingRepository->saveMany($bookings);
    }
}