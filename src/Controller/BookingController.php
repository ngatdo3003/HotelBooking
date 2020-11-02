<?php
namespace App\Controller;
use App\Dto\RestMessage;
use App\Repository\BookingRepository;
use App\Repository\RoomTypeRepository;
use App\Utils\AppUtils;
use DateTime;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Booking;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Booking controller.
 * @Route("/api/booking", name="booking")
 */
class BookingController extends AbstractController
{
    private $repository;
    private $validator;
    private $appUtils;
    private $roomTypeRepository;

    /**
     * @param BookingRepository $repository
     * @param RoomTypeRepository $roomTypeRepository
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(BookingRepository $repository,RoomTypeRepository $roomTypeRepository, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->roomTypeRepository = $roomTypeRepository;
        $this->appUtils = new AppUtils($serializer);
    }

    /**
     * Lists all Bookings.
     * @Rest\Get("")
     * @return Response
     */

    public function getAll()
    {
        $bookings = $this->repository->findall();
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $bookings)));
    }

    /**
     * Find Booking by id
     * @Rest\Get("/{id}")
     * @param $id
     * @return Response
     */

    public function getOne($id)
    {
        $booking = $this->repository->find($id);
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $booking)));
    }


    /**
     * Save Booking.
     * @Rest\Post("")
     *
     * @param Request $request
     * @return Response
     */
    public function post(Request $request)
    {
        try {
            $booking = $this->appUtils->toObj($request->getContent(), Booking::class);
            if($booking == null){
                return new Response($this->appUtils->toJson(new RestMessage(true,"Booking doesn't exist",null)));
            }
            $errors = $this->validator->validate($booking, null, []);
            if($errors->count()>0){
                return new Response($this->appUtils->toJson(new RestMessage(true,$this->appUtils->getErrorMessages($errors),null)));
            }
            if ($booking->getId() == null){
                $booking->setCreatedAt(new DateTime());
            }else{
                $booking->setUpdatedAt(new DateTime());
            }
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($booking);
//            $em->flush();
            return new Response($this->appUtils->toJson(new RestMessage(false, "Save booking successfully!", $booking)));

        }catch(\Exception $e){
            return new Response($this->appUtils->toJson(new RestMessage(true, $e->getMessage(), null)));
        }
    }

    /**
     * Removes the Booking resource
     * @Rest\Delete("/{id}")
     * @param $id
     * @return Response
     */
    public function delete($id)
    {
        $booking = $this->repository->find($id);

        if ($booking) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($booking);
            $em->flush();
            return new Response($this->appUtils->toJson(new RestMessage(false, "Delete booking id = ".$id." successfully!", null)));
        }

        return new Response($this->appUtils->toJson(new RestMessage(true, "Booking id = ".$id." doesn't exist.", null)));

    }

}