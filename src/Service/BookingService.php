<?php

namespace App\Service;

use App\Repository\BookingRepository;
use DateTimeInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

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

    public function getAllReservations(): array
    {
        return $this->bookingRepository->findAll();
    }

    public function findByInterval(DateTimeInterface $from, DateTimeInterface $to): array
    {
        return $this->bookingRepository->findByInterval($from, $to);
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