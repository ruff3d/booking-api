<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Service\BookingService;
use DateTime;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @QueryParam(name="booked_from", allowBlank=false, strict=true, requirements=@Assert\DateTime, description="Time from.")
     * @QueryParam(name="booked_to", allowBlank=false, strict=true, requirements=@Assert\DateTime, description="Time to.")
     * @param ParamFetcher $paramFetcher
     *
     * @return Booking[]
     * @throws \Exception
     */
    public function bookingList(ParamFetcher $paramFetcher): array
    {
        $from = new DateTime($paramFetcher->get('booked_from'));
        $to =  new DateTime($paramFetcher->get('booked_to'));
        $bookings = $this->bookingService->findByInterval($from, $to);

        if (!$bookings) {
            throw new ResourceNotFoundException('There are no bookings for this interval');
        }
        return $bookings;
    }

    /**
     * @Route("/booking", methods={"POST"})
     * @param Request $request
     *
     * @return Response
     */
    public function bookingPlace(Request $request): Response
    {
        $body = $request->getContent();
        $bookings = $this->bookingService->getAllReservations();
        return $this->handleView($this->view([]));
    }

}