<?php

namespace App\Validator;

use App\Entity\Booking;
use App\Service\BookingService;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Doctrine\ORM\{NonUniqueResultException, NoResultException};

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
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint RoomAvailability */
        $bookedFrom = $value->getBookedFrom();
        $bookedTo = $value->getBookedTo();

        if (!($bookedFrom && $bookedTo)) {
            return;
        }

        if (!$this->bookingService->isAvailable($bookedFrom, $bookedTo)) {
            $this->context->buildViolation($constraint->message,
                [
                    '{{ from }}' => $bookedFrom->format('Y-m-d h:m:s'),
                    '{{ to }}'   => $bookedTo->format('Y-m-d h:m:s')
                ])
                ->setCode(202)
                ->addViolation();
        }
    }
}
