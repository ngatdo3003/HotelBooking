<?php
namespace App\Controller;
use App\Dto\RestMessage;
use App\Dto\RoomRecommend;
use App\Repository\RoomRepository;
use App\Repository\RoomTypeRepository;
use App\Utils\AppUtils;
use DateTime;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Room;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Room controller.
 * @Route("/api/room", name="room")
 */
class RoomController extends AbstractController
{
    private $repository;
    private $validator;
    private $appUtils;
    private $roomTypeRepository;

    /**
     * @param RoomRepository $repository
     * @param RoomTypeRepository $roomTypeRepository
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(RoomRepository $repository,RoomTypeRepository $roomTypeRepository, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->roomTypeRepository = $roomTypeRepository;
        $this->appUtils = new AppUtils($serializer);
    }

    /**
     * Lists all Rooms.
     * @Rest\Get("")
     * @return Response
     */

    public function getAll()
    {
        $rooms = $this->repository->findall();
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $rooms)));
    }

    /**
     * Find Room available
     * @Rest\Get("/avail")
     * @QueryParam (name = "start_date")
     * @QueryParam (name = "end_date")
     * @param Request $request
     * @return Response
     */

    public function getAvail(Request $request){
        $start_date = $request->query->get('start_date');
        $end_date = $request->query->get('end_date');
        $rooms = $this->repository->findAvailableRoom($start_date, $end_date);
        $roomRecommends = array();
        foreach($rooms as $room){
            $price = $this->roomTypeRepository->caculatePrice($room->getRoomType()->getId(), $start_date, $end_date);
            $roomRecommend = new RoomRecommend($room->getId(), $room->getName(), $room->getRoomType(), $price);
            array_push($roomRecommends, $roomRecommend);
        }
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $roomRecommends)));
    }
    /**
     * Find Room by id
     * @Rest\Get("/{id}")
     * @param $id
     * @return Response
     */

    public function getOne($id)
    {
        $room = $this->repository->find($id);
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $room)));
    }


    /**
     * Save Room.
     * @Rest\Post("")
     *
     * @param Request $request
     * @return Response
     */
    public function post(Request $request)
    {
        try {
            $room = $this->appUtils->toObj($request->getContent(), Room::class);
            if($room == null){
                return new Response($this->appUtils->toJson(new RestMessage(true,"Room doesn't exist",null)));
            }
            $errors = $this->validator->validate($room, null, []);
            if($errors->count()>0){
                return new Response($this->appUtils->toJson(new RestMessage(true,$this->appUtils->getErrorMessages($errors),null)));
            }
            if ($room->getId() == null){
                $room->setCreatedAt(new DateTime());
            }else{
                $room->setUpdatedAt(new DateTime());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($room);
            $em->flush();
            return new Response($this->appUtils->toJson(new RestMessage(false, "Save room successfully!", $room)));

        }catch(\Exception $e){
            return new Response($this->appUtils->toJson(new RestMessage(true, $e->getMessage(), null)));
        }
    }

    /**
     * Removes the Room resource
     * @Rest\Delete("/{id}")
     * @param $id
     * @return Response
     */
    public function delete($id)
    {
        $room = $this->repository->find($id);

        if ($room) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($room);
            $em->flush();
            return new Response($this->appUtils->toJson(new RestMessage(false, "Delete room id = ".$id." successfully!", null)));
        }

        return new Response($this->appUtils->toJson(new RestMessage(true, "Room id = ".$id." doesn't exist.", null)));

    }

}