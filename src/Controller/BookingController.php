<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Service\BookingService;
use DateTime;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations\{QueryParam, Route};
use FOS\RestBundle\Request\ParamFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     *  @SWG\Get(
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @Model(type=App\Entity\Booking::class)
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input"
     *     )
     * )
     * @Cache(maxage="10")
     * @Route("/booking", methods={"GET"})
     * @QueryParam(name="booked_from", allowBlank=false, strict=true, requirements=@Assert\DateTime(format="Y-m-d"),
     *     description="Time from.")
     * @QueryParam(name="booked_to", allowBlank=false, strict=true, requirements=@Assert\DateTime(format="Y-m-d"),
     *     description="Time to.")
     * @param ParamFetcher $paramFetcher
     *
     * @return Booking[]
     * @throws \Exception
     */
    public function bookingList(ParamFetcher $paramFetcher): array
    {
        $from = new DateTime($paramFetcher->get('booked_from'));
        $to = new DateTime($paramFetcher->get('booked_to'));
        $bookings = $this->bookingService->findByInterval($from, $to);
        if (!$bookings) {
            throw new HttpException(405, 'There are no bookings for this interval');
        }

        return $bookings;
    }

    /**
     *  @SWG\Post(
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="array",
     *             @Model(type=App\Entity\Booking::class)
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @Model(type=App\Entity\Booking::class)
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=202,
     *         description="Booking not possible, room is not available"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input"
     *    )
     * )
     *
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
                if ($violation->getCode() === 202) {
                    throw new HttpException($violation->getCode() , $violation->getMessage());
                }
                $messages .= $violation->getPropertyPath() .': '. $violation->getMessage() . ' ';
            }

            throw new HttpException(405, $messages);
        }
        try {
            $this->bookingService->saveMany($bookings);
        } catch (\Exception $exception) {
            throw new HttpException(500,'Something happened');
        }
        return $bookings;
    }

}