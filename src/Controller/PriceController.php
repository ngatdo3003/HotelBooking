<?php
namespace App\Controller;
use App\Dto\PriceDto;
use App\Dto\RestMessage;
use App\Entity\RoomType;
use App\Repository\PriceRepository;
use App\Repository\RoomTypeRepository;
use App\Utils\AppUtils;
use DateTime;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Price;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Price controller.
 * @Route("/api/price", name="price")
 */
class PriceController extends AbstractController
{
    private $repository;
    private $roomTypeRepository;
    private $validator;
    private $appUtils;

    /**
     * @param PriceRepository $repository
     * @param RoomTypeRepository $roomTypeRepository
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(PriceRepository $repository, RoomTypeRepository $roomTypeRepository, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->roomTypeRepository = $roomTypeRepository;
        $this->repository = $repository;
        $this->validator = $validator;
        $this->appUtils = new AppUtils($serializer);
    }

    /**
     * Lists all Prices.
     * @Rest\Get("")
     * @return Response
     */

    public function getAll()
    {
        $prices = $this->repository->findall();
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $prices)));
    }

    /**
     * Find Price by id
     * @Rest\Get("/{id}")
     * @param $id
     * @return Response
     */

    public function getOne($id)
    {
        $price = $this->repository->find($id);
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $price)));
    }



    /**
     * Save Price.
     * @Rest\Post("")
     *
     * @param Request $request
     * @return Response
     */
    public function post(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);

            $p = $data['price'];
            $start_date = (new \DateTime($data['start_date']))->setTime(0,0,0);
            $end_date = (new \DateTime($data["end_date"]))->setTime(0,0,0);
            $room_type = $this->roomTypeRepository->find($data["room_type_id"]);

            $dayInMilisecond = new \DateInterval("P1D");
            $prices = array();

            for($i = 0; $i <=(int) (date_diff($start_date, $end_date)->days) +1; $i++){
                $price = $this->repository->findOneByRoomTypeAndDate($data["room_type_id"], $start_date);
                if ($price == null){
                    $price = new Price();
                    $price->setRoomType($room_type);
                    $price->setPrice($p);
                    $price->setDate($start_date);
                    $price->setCreatedAt(new DateTime());
                }else {
                    $price->setPrice($p);
                    $price->setUpdatedAt(new DateTime());
                }
                array_push($prices, $price);
                $em = $this->getDoctrine()->getManager();
                $em->persist($price);
                $em->flush();
                $start_date = $start_date->add($dayInMilisecond);

            }
            return new Response($this->appUtils->toJson(new RestMessage(false, "Save price successfully!", $prices)));

        }catch(\Exception $e){
            return new Response($this->appUtils->toJson(new RestMessage(true, $e->getMessage(), null)));
        }
    }

    /**
     * Removes the Price resource
     * @Rest\Delete("/{id}")
     * @param $id
     * @return Response
     */
    public function delete($id)
    {
        $price = $this->repository->find($id);

        if ($price) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($price);
            $em->flush();
            return new Response($this->appUtils->toJson(new RestMessage(false, "Delete price id = ".$id." successfully!", null)));
        }

        return new Response($this->appUtils->toJson(new RestMessage(true, "Price id = ".$id." doesn't exist.", null)));

    }

}