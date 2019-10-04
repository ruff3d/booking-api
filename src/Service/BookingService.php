<?php

namespace App\Service;

use App\Repository\BookingRepository;
use DateTimeInterface;

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
        $this->bookingRepository->findByInterval($from, $to);
    }
}