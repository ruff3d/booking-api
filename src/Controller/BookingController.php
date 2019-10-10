<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Service\BookingService;
use DateTime;
use FOS\RestBundle\{
    Controller\AbstractFOSRestController,
    Controller\Annotations\QueryParam,
    Controller\Annotations\Route
};
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{ParamConverter, Cache};
use Swagger\Annotations as SWG;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\{Constraints as Assert, ConstraintViolationListInterface};

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
     * @SWG\Get(
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
     * @Cache(maxage="15")
     * @Route("/booking", methods={"GET"})
     * @QueryParam(
     *     key="booked_from",
     *     name="bookedFrom",
     *     allowBlank=false,
     *     strict=true,
     *     requirements=@Assert\Regex("/^\d{4}-\d{1,2}-\d{1,2}.*?/"),
     *     description="Time from."
     * )
     * @QueryParam(
     *     key="booked_to",
     *     name="bookedTo",
     *     allowBlank=false,
     *     strict=true,
     *     requirements=@Assert\Regex("/^\d{4}-\d{1,2}-\d{1,2}.*?/"),
     *     description="Time to."
     * )
     * @param DateTime $bookedFrom
     * @param DateTime $bookedTo
     *
     * @return Booking[]
     * @throws \Exception
     */
    public function bookingList(DateTime $bookedFrom, DateTime $bookedTo): array
    {
        $bookings = $this->bookingService->findByInterval($bookedFrom, $bookedTo);
        if (!$bookings) {
            throw new HttpException(405, 'There are no bookings for this interval');
        }

        return $bookings;
    }

    /**
     * @SWG\Post(
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
                    throw new HttpException($violation->getCode(), $violation->getMessage());
                }
                $messages .= $violation->getPropertyPath() . ': ' . $violation->getMessage() . ' ';
            }

            throw new HttpException(405, $messages);
        }
        try {
            $this->bookingService->saveMany($bookings);
        } catch (\Exception $exception) {
            throw new HttpException(500, 'Something happened');
        }

        return $bookings;
    }

}