<?php

namespace App\Tests;

use App\Entity\Booking;
use App\Service\BookingService;
use App\Validator\RoomAvailability;
use App\Validator\RoomAvailabilityValidator;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class RoomAvailabilityValidatorTest extends TestCase
{
    /**
     * @var MockObject|ExecutionContext
     */
    private $contextMock;

    /**
     * @var RoomAvailabilityValidator
     */
    private $validator;

    /**
     * @var BookingService|MockObject
     */
    private $bookingServiceMock;

    /**
     * @var MockObject|ConstraintViolationBuilderInterface
     */
    private $constraintViolationBuilderMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(ExecutionContext::class);
        $this->constraintViolationBuilderMock = $this->createMock(ConstraintViolationBuilder::class);
        $this->bookingServiceMock = $this->createMock(BookingService::class);
        $this->validator = new RoomAvailabilityValidator($this->bookingServiceMock);
        $this->validator->initialize($this->contextMock);
    }

    /**
     * @dataProvider availabilityDataProvider
     * @param $isAvailable
     * @param $expectAvailable
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testValidation($isAvailable, $expectAvailable): void
    {
        $this->bookingServiceMock
            ->expects(self::once())
            ->method('isAvailable')
            ->willReturn($isAvailable);

        $booking = new Booking();
        $booking
            ->setBookedFrom(new DateTime())
            ->setBookedTo(new DateTime());

        $this->constraintViolationBuilderMock
            ->expects(self::exactly($expectAvailable))
            ->method('setCode')
            ->with(202)
            ->willReturn($this->constraintViolationBuilderMock);

        $this->contextMock
            ->expects(self::exactly($expectAvailable))
            ->method('buildViolation')
            ->willReturn($this->constraintViolationBuilderMock);

        
        $this->validator->validate($booking, new RoomAvailability());
    }

    public function availabilityDataProvider(): array
    {
        return [
            [true, 0],
            [false, 1],
        ];
    }
}
