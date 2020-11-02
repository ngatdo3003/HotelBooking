<?php
namespace App\Controller;
use App\Dto\RestMessage;
use App\Repository\RoomTypeRepository;
use App\Utils\AppUtils;
use DateTime;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\RoomType;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * RoomType controller.
 * @Route("/api/roomType", name="roomType")
 */
class RoomTypeController extends AbstractController
{
    private $repository;
    private $validator;
    private $appUtils;

    /**
     * @param RoomTypeRepository $repository
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(RoomTypeRepository $repository, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->appUtils = new AppUtils($serializer);
    }

    /**
     * Lists all RoomTypes.
     * @Rest\Get("")
     * @return Response
     */

    public function getAll()
    {
        $roomTypes = $this->repository->findall();
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $roomTypes)));
    }

    /**
     * Find RoomType by id
     * @Rest\Get("/{id}")
     * @param $id
     * @return Response
     */

    public function getOne($id)
    {
        $roomType = $this->repository->find($id);
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $roomType)));
    }



    /**
     * Save RoomType.
     * @Rest\Post("")
     *
     * @param Request $request
     * @return Response
     */
    public function post(Request $request)
    {
        try {
            $roomType = $this->appUtils->toObj($request->getContent(), RoomType::class);
            if($roomType == null){
                return new Response($this->appUtils->toJson(new RestMessage(true,"RoomType doesn't exist",null)));
            }
            $errors = $this->validator->validate($roomType, null, []);
            if($errors->count()>0){
                return new Response($this->appUtils->toJson(new RestMessage(true,$this->appUtils->getErrorMessages($errors),null)));
            }
            if ($roomType->getId() == null){
                $roomType->setCreatedAt(new DateTime());
            }else{
                $roomType->setUpdatedAt(new DateTime());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($roomType);
            $em->flush();
            return new Response($this->appUtils->toJson(new RestMessage(false, "Save roomType successfully!", $roomType)));

        }catch(\Exception $e){
            return new Response($this->appUtils->toJson(new RestMessage(true, $e->getMessage(), null)));
        }
    }

    /**
     * Removes the RoomType resource
     * @Rest\Delete("/{id}")
     * @param $id
     * @return Response
     */
    public function delete($id)
    {
        $roomType = $this->repository->find($id);

        if ($roomType) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($roomType);
            $em->flush();
            return new Response($this->appUtils->toJson(new RestMessage(false, "Delete roomType id = ".$id." successfully!", null)));
        }

        return new Response($this->appUtils->toJson(new RestMessage(true, "RoomType id = ".$id." doesn't exist.", null)));

    }

}