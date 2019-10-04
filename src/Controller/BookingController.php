<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Service\BookingService;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Request\ParamFetcher;
use http\Exception\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\View\View;
//use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Route("/v2")
 */
class BookingController extends AbstractFOSRestController
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
     * @Route("/booking", methods={"GET"})
     * @QueryParam(name="booked_from", allowBlank=false, strict=true, requirements=@Assert\DateTime(format="Y-m-d+"),
     *     description="Time from.")
     * @QueryParam(name="booked_to", allowBlank=false, strict=true, requirements=@Assert\DateTime(format="Y-m-d+"),
     *     description="Time to.")
     * @param ParamFetcher $paramFetcher
     *
     * @return Booking[]|View
     * @throws \Exception
     */
    public function bookingList(ParamFetcher $paramFetcher)
    {
        $from = new DateTime($paramFetcher->get('booked_from'));
        $to = new DateTime($paramFetcher->get('booked_to'));
        $bookings = $this->bookingService->findByInterval($from, $to);
        if (!$bookings) {
            return new View('There are no bookings for this interval', Response::HTTP_NOT_FOUND);
        }

        return $bookings;
    }

    /**
     * @Route("/booking", methods={"POST"})
     * @ParamConverter("bookings", class="App\Entity\Booking[]", converter="fos_rest.request_body")
     * @param Booking[] $bookings
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return Booking[]
     */
    public function bookingPlace(array $bookings, ConstraintViolationListInterface $validationErrors): array
    {
        if ($validationErrors->count() !== 0) {
            $messages = '';
            foreach ($validationErrors as $violation) {
                $messages .= $violation->getMessage() . PHP_EOL;
            }

            throw new HttpException(Response::HTTP_METHOD_NOT_ALLOWED, $messages);
        }
        try {
            $this->bookingService->saveMany($bookings);
        } catch (\Exception $exception) {
            throw new HttpException(500,'There is DB issue');
        }
        return $bookings;
    }

}