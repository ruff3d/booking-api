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

        if ($this->bookingService->findByInterval($value->getBookedFrom(), $value->getBookedTo())->count() > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
