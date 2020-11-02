<?php
namespace App\Controller;
use App\Dto\RestMessage;
use App\Repository\UserRepository;
use App\Utils\AppUtils;
use DateTime;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * User controller.
 * @Route("/api/user", name="user")
 */
class UserController extends AbstractController
{
    private $repository;
    private $validator;
    private $appUtils;

    /**
     * @param UserRepository $repository
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(UserRepository $repository, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->appUtils = new AppUtils($serializer);
    }

    /**
     * Lists all Users.
     * @Rest\Get("")
     * @return Response
     */

    public function getAll()
    {
        $users = $this->repository->findall();
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $users)));
    }

    /**
     * Find User by id
     * @Rest\Get("/{id}")
     * @param $id
     * @return Response
     */

    public function getOne($id)
    {
        $user = $this->repository->find($id);
        return new Response($this->appUtils->toJson(new RestMessage(false, null, $user)));
    }


    /**
     * Save User.
     * @Rest\Post("")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function post(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        try {
            $user = $this->appUtils->toObj($request->getContent(), User::class);
            if($user == null){
                return new Response($this->appUtils->toJson(new RestMessage(true,"User doesn't exist",null)));
            }
            $errors = $this->validator->validate($user, null, []);
            if($errors->count()>0){
                return new Response($this->appUtils->toJson(new RestMessage(true,$this->appUtils->getErrorMessages($errors),null)));
            }
            if ($user->getId() == null){
                $user->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $user->getPassword()
                ));
                $user->setEnable(1);
                $user->setCreatedAt(new DateTime());
                $user->setBalance(0);
            }else{
                $user->setUpdatedAt(new DateTime());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new Response($this->appUtils->toJson(new RestMessage(false, "Save user successfully!", $user)));

        }catch(\Exception $e){
            return new Response($this->appUtils->toJson(new RestMessage(true, $e->getMessage(), null)));
        }
    }

    /**
     * Removes the User resource
     * @Rest\Delete("/{id}")
     * @param $id
     * @return Response
     */
    public function delete($id)
    {
        $user = $this->repository->find($id);

        if ($user) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            return new Response($this->appUtils->toJson(new RestMessage(false, "Delete user id = ".$id." successfully!", null)));
        }

        return new Response($this->appUtils->toJson(new RestMessage(true, "User id = ".$id." doesn't exist.", null)));

    }

}