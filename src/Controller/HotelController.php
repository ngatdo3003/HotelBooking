<?php
namespace App\Controller;
use App\Dto\RestMessage;
use App\Repository\HotelRepository;
use App\Utils\AppUtils;
use DateTime;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Hotel;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Hotel controller.
 * @Route("/api/hotel", name="hotel")
 */
class HotelController extends AbstractController
{
    private $repository;
    private $validator;
    private $appUtils;

    /**
     * @param HotelRepository $repository
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(HotelRepository $repository, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->appUtils = new AppUtils($serializer);
    }

    /**
     * Lists all Hotels.
     * @Rest\Get("")
     * @param Request $request
     * @return Response
     */

    public function getAll(Request $request)
    {
        $sortBy = $request->query->get('sortBy');

        $hotels = $this->repository->findall();
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $hotels)));
    }

    /**
     * Find Hotel by id
     * @Rest\Get("/{id}")
     * @param $id
     * @return Response
     */

    public function getOne($id)
    {
        $hotel = $this->repository->find($id);
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $hotel)));
    }



    /**
     * Save Hotel.
     * @Rest\Post("")
     *
     * @param Request $request
     * @return Response
     */
    public function post(Request $request)
    {
        try {
            $hotel = $this->appUtils->toObj($request->getContent(), Hotel::class);
            if($hotel == null){
                return new Response($this->appUtils->toJson(new RestMessage(true,"Hotel doesn't exist",null)));
            }
            $errors = $this->validator->validate($hotel, null, []);
            if($errors->count()>0){
                return new Response($this->appUtils->toJson(new RestMessage(true,$this->appUtils->getErrorMessages($errors),null)));
            }
            if ($hotel->getId() == null){
                $hotel->setCreatedAt(new DateTime());
            }else{
                $hotel->setUpdatedAt(new DateTime());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($hotel);
            $em->flush();
            return new Response($this->appUtils->toJson(new RestMessage(false, "Save hotel successfully!", $hotel)));

        }catch(\Exception $e){
            return new Response($this->appUtils->toJson(new RestMessage(true, $e->getMessage(), null)));
        }
    }

    /**
     * Removes the Hotel resource
     * @Rest\Delete("/{id}")
     * @param $id
     * @return Response
     */
    public function delete($id)
    {
        $hotel = $this->repository->find($id);

        if ($hotel) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($hotel);
            $em->flush();
            return new Response($this->appUtils->toJson(new RestMessage(false, "Delete hotel id = ".$id." successfully!", null)));
        }

        return new Response($this->appUtils->toJson(new RestMessage(true, "Hotel id = ".$id." doesn't exist.", null)));

    }

}