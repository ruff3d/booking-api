<?php

namespace App\Validator;

use App\Entity\Booking;
use App\Service\BookingService;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};

class RoomAvailabilityValidator extends ConstraintValidator
{
    /**
     * @var BookingService
     */
    private $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * @param Booking $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint RoomAvailability */
        $bookedFrom = $value->getBookedFrom();
        $bookedTo = $value->getBookedTo();
        if (count($this->bookingService->findByInterval($bookedFrom, $bookedTo)) > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ from }}', $bookedFrom->format('Y-m-d h:m'))
                ->setParameter('{{ to }}', $bookedTo->format('Y-m-d h:m'))
                ->addViolation();
        }
    }
}
